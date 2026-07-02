<?php

namespace App\Repository;

use App\Entity\Stay;
use Doctrine\Persistence\ObjectRepository;

/**
 * Port de persistance des {@see Stay} (skill §3 Repository rules).
 *
 * Étend ObjectRepository pour exposer find/findAll/findBy/findOneBy utilisés
 * par les contrôleurs (en plus de findStockById utilisé par les services).
 *
 * @extends ObjectRepository<Stay>
 */
interface StayRepositoryInterface extends ObjectRepository
{
    /**
     * @return int stock réel (disponible) du séjour identifié
     */
    public function findStockById(int $idStay): int;
}
