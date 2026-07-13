<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Persistence\ObjectRepository;

/**
 * Persistence port for {@see User} (skill §3 Repository rules).
 * Exposes find/findOneBy methods used by SecurityController.
 *
 * @extends ObjectRepository<User>
 */
interface UserRepositoryInterface extends ObjectRepository
{
}
