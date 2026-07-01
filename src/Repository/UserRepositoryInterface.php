<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Persistence\ObjectRepository;

/**
 * Port de persistance des {@see User} (skill §3 Repository rules).
 * Expose find/findOneBy utilisés par SecurityController.
 *
 * @extends ObjectRepository<User>
 */
interface UserRepositoryInterface extends ObjectRepository
{
}
