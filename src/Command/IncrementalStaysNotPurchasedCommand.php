<?php

namespace App\Command;

use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:unpaidorder', description: 'Cancel reservations not purchased within 15 minutes and release seats')]
final class IncrementalStaysNotPurchasedCommand extends Command
{
    public function __construct(
        private readonly ReservationRepository $repo,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $notPurchased = $this->repo->findUnpaid();
        $seats = 0;
        $now = new \DateTime();
        foreach ($notPurchased as $reservation) {
            $date = $reservation->getCreatedDate();
            if (null === $date) {
                continue;
            }

            $interval = ($now->getTimestamp() - $date->getTimestamp()) / 60;

            if ($interval > 15) {
                $stay = $reservation->getStays()->first();
                if (!$stay instanceof \App\Entity\Stays) {
                    continue;
                }
                $stock = $stay->getStock();
                $nbTravelers = count($reservation->getTravelers());
                $seats += $nbTravelers;
                $stay->setStock($stock + $nbTravelers);
                $this->entityManager->remove($reservation);
                $this->entityManager->flush();
            }
        }

        $io->success($seats.' seats in '.count($notPurchased).' reservations not purchased removed');

        return Command::SUCCESS;
    }
}
