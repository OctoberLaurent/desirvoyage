<?php

namespace App\Repository;

/**
 * Port de persistance des {@see \App\Entity\Stay} (skill §3 Repository rules).
 */
interface StayRepositoryInterface
{
    /**
     * @return int stock réel (disponible) du séjour identifié
     */
    public function findStockById(int $idStay): int;
}
