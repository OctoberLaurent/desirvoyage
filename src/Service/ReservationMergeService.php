<?php

namespace App\Service;

use App\Entity\Reservation;
use App\Entity\Traveler;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

class ReservationMergeService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $em){
        $this->entityManager = $em;
    }

    /**
     * Reconstructs travelers attributes
     *
     * @param $reservation
     * @return void
     */
    public function reservationMerge($reservation) : Reservation
    {
        $merged = $this->entityManager->merge($reservation);
        $merged->setTravelers( $reservation->getTravelers() );
        $merged->setOptions( $reservation->getOptions() );
        
        $stays = $reservation->getStays();
        $mstays = new ArrayCollection();
        foreach( $stays as $stay ){
            $mstays[] = $this->entityManager->merge( $stay );
        }
        $merged->setStays( $mstays );

        return $merged;
    }

    /**
     * Reconstructs option attributes
     *
     * @param $reservation
     * @return void
     */
    public function reservationOptionsMerge($reservation) : void
    {
        $options = $reservation->getOptions();
        $moptions = new ArrayCollection();
        foreach( $options as $option ){
            $moptions[] = $this->entityManager->merge( $option );
        }
        $reservation->setOptions( $moptions );
    }

}