<?php

namespace App\Repository;

use Doctrine\Persistence\ObjectRepository;

/**
 * Persistence port for {@see \App\Entity\Category} (skill §3 Repository rules).
 * Marker interface (no custom methods) exposing standard ObjectRepository methods
 * (find/findAll/findBy/findOneBy) used by controllers.
 *
 * @extends ObjectRepository<\App\Entity\Category>
 */
interface CategoryRepositoryInterface extends ObjectRepository
{
}
