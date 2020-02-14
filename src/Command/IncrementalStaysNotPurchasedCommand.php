<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReservationRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class IncrementalStaysNotPurchasedCommand extends Command
{
    protected static $defaultName = 'app:StaysNotPurchasedCommand';
    private $entityManager;

    public function __construct(ReservationRepository $repo, EntityManagerInterface $em)
    {
        $this->repo = $repo;
        $this->entityManager = $em;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Add stays not purshased')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $notPurshased = $this->repo->findBy(
           ["payment" => null]
         );
        $seats = 0;
        foreach( $notPurshased as $reservation){
        // TODO
        $stay = $reservation->getStays()[0];
        $stock = $stay->getStock();
        $traveler = $reservation->getTravelers();
        $nbTravelers = count($traveler);
        $seats .= $nbTravelers;
        $stay->setStock($stock+$nbTravelers);
        $this->entityManager->remove($reservation);
        $this->entityManager->flush();
        }
        $output->writeln($seats.' saets in '.count($notPurshased).' reservations not purshased removed ');
        
        $io->success(' Success ');
        return 0;
    }
}
