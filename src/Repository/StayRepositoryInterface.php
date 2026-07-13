<?php

namespace App\Repository;

use App\Entity\Stay;
use Doctrine\Persistence\ObjectRepository;

/**
 * Persistence port for {@see Stay} (skill §3 Repository rules).
 *
 * Extends ObjectRepository to expose find/findAll/findBy/findOneBy methods used
 * by controllers, in addition to findStockById used by services.
 *
 * @extends ObjectRepository<Stay>
 */
interface StayRepositoryInterface extends ObjectRepository
{
    /**
     * @return int actual available stock for the identified stay
     */
    public function findStockById(int $idStay): int;

    /** Loads the stay with a write lock in an active transaction. */
    public function findForUpdate(int $idStay): ?Stay;
}
