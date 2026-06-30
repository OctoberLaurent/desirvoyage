<?php

namespace App\Service;

use App\Entity\Option;
use App\Entity\Reservation;
use App\Entity\Stay;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

final readonly class ReservationMergeService
{
    public function __construct(private EntityManagerInterface $entityManager)
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
     * @param Collection<int, Option> $options
     *
     * @return ArrayCollection<int, Option>
     */
    private function managedOptions(Collection $options): ArrayCollection
    {
        /** @var ArrayCollection<int, Option> $managed */
        $managed = new ArrayCollection();
        foreach ($options as $option) {
            $id = $option->getId();
            $managed[] = null !== $id ? ($this->entityManager->find(Option::class, $id) ?? $option) : $option;
        }

        return $managed;
    }

    /**
     * @param Collection<int, Stay> $stays
     *
     * @return ArrayCollection<int, Stay>
     */
    private function managedStays(Collection $stays): ArrayCollection
    {
        /** @var ArrayCollection<int, Stay> $managed */
        $managed = new ArrayCollection();
        foreach ($stays as $stay) {
            $id = $stay->getId();
            $managed[] = null !== $id ? ($this->entityManager->find(Stay::class, $id) ?? $stay) : $stay;
        }

        return $managed;
    }
}
