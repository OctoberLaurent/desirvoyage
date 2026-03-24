<?php

namespace App\Command;

use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class IncrementalStaysNotPurchasedCommand extends Command
{
    protected static $defaultName = 'app:unpaidorder';
    private ReservationRepository $repo;
    private EntityManagerInterface $entityManager;

    public function __construct(ReservationRepository $repo, EntityManagerInterface $em)
    {
        $this->repo = $repo;
        $this->entityManager = $em;
        parent::__construct();
    }

    #[\Override]
    protected function configure()
    {
        $this->setDescription('Add stays not purshased');
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $notPurshased = $this->repo->findBy(
            ['payment' => null]
        );
        $seats = 0;
        $interval = 0;
        foreach ($notPurshased as $reservation) {
            $now = new \DateTime();
            $date = $reservation->getCreatedDate();
            $date1 = $now->getTimestamp();
            $date2 = $date->getTimestamp();
            $interval = (($date1 - $date2) / 60);

            if ($interval > 15) {
                $stay = $reservation->getStays()[0];
                $stock = $stay->getStock();
                $traveler = $reservation->getTravelers();

                $nbTravelers = count($traveler);
                $seats .= $nbTravelers;
                $stay->setStock($stock + $nbTravelers);
                $this->entityManager->remove($reservation);
                $this->entityManager->flush();
            }
        }
        // TODO Erase it
        dump($interval);
        $output->writeln($seats.' saets in '.count($notPurshased).' reservations not purshased removed ');

        $io->success(' Success ');

        return 0;
    }
}
