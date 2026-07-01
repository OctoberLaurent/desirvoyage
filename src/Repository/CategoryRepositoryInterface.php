<?php

namespace App\Repository;

use Doctrine\Persistence\ObjectRepository;

/**
 * Port de persistance des {@see \App\Entity\Category} (skill §3 Repository rules).
 * Marqueur (aucune méthode custom) — expose les méthodes standard d'ObjectRepository
 * (find/findAll/findBy/findOneBy) utilisées par les contrôleurs.
 *
 * @extends ObjectRepository<\App\Entity\Category>
 */
interface CategoryRepositoryInterface extends ObjectRepository
{
}
