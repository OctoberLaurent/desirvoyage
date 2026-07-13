<?php

namespace App\Repository;

use App\Entity\Travel;
use Doctrine\Persistence\ObjectRepository;

/**
 * Persistence port for {@see Travel} (skill §3 Repository rules).
 * Extends ObjectRepository to expose find/findAll/findBy/findOneBy methods used
 * by controllers.
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
