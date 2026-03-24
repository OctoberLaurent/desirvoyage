<?php

namespace App\Service;

use App\Entity\Options;
use App\Entity\Reservation;
use App\Entity\Stays;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

final class ReservationMergeService
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * Reconstructs travelers attributes.
     */
    public function reservationMerge(Reservation $reservation): Reservation
    {
        // If the reservation has an ID, reload it from DB to ensure it's managed
        if (null !== $reservation->getId()) {
            $merged = $this->entityManager->find(Reservation::class, $reservation->getId()) ?? $reservation;
        } else {
            $merged = $reservation;
        }

        $merged->setTravelers($reservation->getTravelers());
        $merged->setOptions($reservation->getOptions());

        $stays = $reservation->getStays();
        $mstays = new ArrayCollection();
        foreach ($stays as $stay) {
            $managedStay = null !== $stay->getId()
                ? ($this->entityManager->find(Stays::class, $stay->getId()) ?? $stay)
                : $stay;
            $mstays[] = $managedStay;
        }
        $merged->setStays($mstays);

        return $merged;
    }

    /**
     * Reconstructs option attributes.
     */
    public function reservationOptionsMerge(Reservation $reservation): void
    {
        $options = $reservation->getOptions();
        $moptions = new ArrayCollection();
        foreach ($options as $option) {
            $managedOption = null !== $option->getId()
                ? ($this->entityManager->find(Options::class, $option->getId()) ?? $option)
                : $option;
            $moptions[] = $managedOption;
        }
        $reservation->setOptions($moptions);
    }
}
