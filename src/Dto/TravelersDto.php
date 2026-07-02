<?php

namespace App\Dto;

use App\Entity\Reservation;
use App\Entity\Traveler;
use App\ValueObject\Email;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * DTO enveloppe la collection de voyageurs pour {@see \App\Form\TravelersType}
 * (skill §8 : le formulaire bind un DTO, pas l'entité {@see Reservation}).
 *
 * - {@see self::fromReservation()} pré-remplit le DTO depuis l'entité (affichage).
 * - {@see self::toTravelers()} reconvertit les DTO validés en entités {@see Traveler}
 *   à la soumission (le contrôleur attache ensuite la collection à la réservation).
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
        $collection = new ArrayCollection();
        foreach ($this->travelers as $t) {
            $traveler = new Traveler();
            $traveler->setLastname($t->lastname ?? '');
            $traveler->setFirstname($t->firstname ?? '');
            // E-mail déjà validé par Assert\Email sur le DTO → construction sûre du VO.
            $traveler->setEmail(new Email($t->email ?? ''));
            if (null !== $t->birthday) {
                $traveler->setBirthday($t->birthday);
            }
            $collection->add($traveler);
        }

        return $collection;
    }
}
