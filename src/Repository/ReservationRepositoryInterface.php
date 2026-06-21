<?php

namespace App\Repository;

use App\Entity\Reservation;

/**
 * Port de persistance des {@see Reservation} (skill §3 Repository rules :
 * « Always define an interface »). Permet d'injecter l'abstraction dans les
 * services (DIP) et de mocker dans les tests.
 */
interface ReservationRepositoryInterface
{
    /**
     * @return array<int, Reservation>
     */
    public function findUnpaid(): array;
}
