<?php

namespace App\Repository;

use App\Entity\Reservation;

/**
 * Persistence port for {@see Reservation} (skill §3 Repository rules:
 * "Always define an interface"). It allows the abstraction to be injected into
 * services (DIP) and mocked in tests.
 */
interface ReservationRepositoryInterface
{
    /**
     * @return array<int, Reservation>
     */
    public function findUnpaid(): array;

    /**
     * @return array<int, Reservation>
     */
    public function findExpiredPending(\DateTimeImmutable $createdBefore): array;
}
