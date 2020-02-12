<?php

namespace App\Service;

use Doctrine\Common\Collections\ArrayCollection;

class ReservationMergeService
{
    public function reservationMerge($entityManager, $reservation)
    {
        $merged = $entityManager->merge($reservation);
        $merged->setTravelers( $reservation->getTravelers() );
        $merged->setOptions( $reservation->getOptions() );
        $stays = $reservation->getStays();
        $mstays = new ArrayCollection();
        foreach( $stays as $stay ){
            $mstays[] = $entityManager->merge( $stay );
        }
        $merged->setStays( $mstays );

        return $merged;
    }

    public function reservationOptionsMerge($entityManager, $reservation)
    {
        $merged = $entityManager->merge($reservation);
        $merged->setTravelers( $reservation->getTravelers() );
        $merged->setOptions( $reservation->getOptions() );
        $stays = $reservation->getStays();
        $mstays = new ArrayCollection();
        foreach( $stays as $stay ){
            $mstays[] = $entityManager->merge( $stay );
        }
        $merged->setStays( $mstays );

        return $merged;
    }
}