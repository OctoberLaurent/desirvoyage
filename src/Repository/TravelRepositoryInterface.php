<?php

namespace App\Repository;

use App\Entity\Travel;
use Doctrine\Persistence\ObjectRepository;

/**
 * Port de persistance des {@see Travel} (skill §3 Repository rules).
 * Étend ObjectRepository pour exposer find/findAll/findBy/findOneBy utilisés
 * par les contrôleurs.
 *
 * @extends ObjectRepository<Travel>
 */
interface TravelRepositoryInterface extends ObjectRepository
{
    /**
     * @return array<int, Travel>
     */
    public function findRandom(int $limit): array;

    /**
     * @param array<string, mixed> $search
     *
     * @return array<int, Travel>
     */
    public function findTravelsByNameAndPrice(array $search): array;
}
