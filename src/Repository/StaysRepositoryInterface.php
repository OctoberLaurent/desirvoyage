<?php

namespace App\Repository;

/**
 * Port de persistance des {@see \App\Entity\Stays} (skill §3 Repository rules).
 */
interface StaysRepositoryInterface
{
    /**
     * @return int stock réel (disponible) du séjour identifié
     */
    public function findStockById(int $idStay): int;
}
