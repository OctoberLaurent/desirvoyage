<?php

namespace App\Repository;

use App\Entity\Stay;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\Persistence\ManagerRegistry;

/** @extends \Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository<Stay> */
class StayRepository extends ServiceEntityRepository implements StayRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stay::class);
    }

    #[\Override]
    public function findStockById(int $idStay): int
    {
        $stay = $this->createQueryBuilder('s')
            ->andWhere('s.id = :id')
            ->setParameter('id', $idStay)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $stay instanceof Stay ? $stay->getStock() : 0;
    }

    #[\Override]
    public function findForUpdate(int $idStay): ?Stay
    {
        return $this->getEntityManager()->find(Stay::class, $idStay, LockMode::PESSIMISTIC_WRITE);
    }
}
