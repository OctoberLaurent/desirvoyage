<?php

namespace App\Dto;

use App\Entity\Reservation;
use App\Entity\Traveler;
use App\ValueObject\Email;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * DTO wrapping the traveler collection for {@see \App\Form\TravelersType}
 * (skill §8: the form binds a DTO, not the {@see Reservation} entity).
 *
 * - {@see self::fromReservation()} populates the DTO from the entity for display.
 * - {@see self::toTravelers()} converts validated DTOs back to {@see Traveler}
 *   entities on submission, then the controller attaches the collection to the reservation.
 */
final class TravelersDto
{
    /** @var array<int, TravelerDto> */
    public array $travelers = [];

    public static function fromReservation(Reservation $reservation): self
    {
        $dto = new self();
        foreach ($reservation->getTravelers() as $traveler) {
            $t = new TravelerDto();
            $t->lastname = $traveler->getLastname();
            $t->firstname = $traveler->getFirstname();
            $t->email = $traveler->getEmail()->value();
            $t->birthday = $traveler->getBirthday();
            $dto->travelers[] = $t;
        }

        return $dto;
    }

    /**
     * @return ArrayCollection<int, Traveler>
     */
    public function toTravelers(): ArrayCollection
    {
        /** @var ArrayCollection<int, Traveler> $collection */
        $collection = new ArrayCollection();
        foreach ($this->travelers as $t) {
            $traveler = new Traveler();
            $traveler->setLastname($t->lastname ?? '');
            $traveler->setFirstname($t->firstname ?? '');
            // The DTO has already passed Assert\Email validation, so creating the value object is safe.
            $traveler->setEmail(new Email($t->email ?? ''));
            if (null !== $t->birthday) {
                $traveler->setBirthday($t->birthday);
            }
            $collection->add($traveler);
        }

        return $collection;
    }
}
