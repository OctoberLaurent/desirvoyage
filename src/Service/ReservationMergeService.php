<?php

namespace App\Service;

use App\Entity\Options;
use App\Entity\Reservation;
use App\Entity\Stays;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

final class ReservationMergeService
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * Reconstruit une réservation avec des entités managées (au lieu des entités
     * détachées issues de la session) pour éviter les erreurs d'association null.
     */
    public function reservationMerge(Reservation $reservation): Reservation
    {
        // Si la réservation a un id, on la recharge depuis la DB pour qu'elle soit managée
        if (null !== $reservation->getId()) {
            $merged = $this->entityManager->find(Reservation::class, $reservation->getId()) ?? $reservation;
        } else {
            $merged = $reservation;
        }

        $merged->setTravelers($reservation->getTravelers());

        $user = $reservation->getUser();
        $managedUser = $this->entityManager->find(User::class, $user->getId());
        if (null !== $managedUser) {
            $merged->setUser($managedUser);
        }

        $merged->setOptions($this->managedOptions($reservation->getOptions()));
        $merged->setStays($this->managedStays($reservation->getStays()));

        return $merged;
    }

    /**
     * Reconstruit uniquement les options managées d'une réservation (session).
     */
    public function reservationOptionsMerge(Reservation $reservation): void
    {
        $reservation->setOptions($this->managedOptions($reservation->getOptions()));
    }

    /**
     * @param Collection<int, Options> $options
     *
     * @return ArrayCollection<int, Options>
     */
    private function managedOptions(Collection $options): ArrayCollection
    {
        /** @var ArrayCollection<int, Options> $managed */
        $managed = new ArrayCollection();
        foreach ($options as $option) {
            $id = $option->getId();
            $managed[] = null !== $id ? ($this->entityManager->find(Options::class, $id) ?? $option) : $option;
        }

        return $managed;
    }

    /**
     * @param Collection<int, Stays> $stays
     *
     * @return ArrayCollection<int, Stays>
     */
    private function managedStays(Collection $stays): ArrayCollection
    {
        /** @var ArrayCollection<int, Stays> $managed */
        $managed = new ArrayCollection();
        foreach ($stays as $stay) {
            $id = $stay->getId();
            $managed[] = null !== $id ? ($this->entityManager->find(Stays::class, $id) ?? $stay) : $stay;
        }

        return $managed;
    }
}
