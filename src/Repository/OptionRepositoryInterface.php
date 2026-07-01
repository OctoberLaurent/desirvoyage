<?php

namespace App\Repository;

use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ObjectRepository;

/**
 * Port de persistance des {@see \App\Entity\Option} (skill §3 Repository rules).
 *
 * @extends ObjectRepository<\App\Entity\Option>
 */
interface OptionRepositoryInterface extends ObjectRepository
{
    public function findOptions(int $travelId): QueryBuilder;
}
