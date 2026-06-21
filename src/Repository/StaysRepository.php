<?php

namespace App\Repository;

use App\Entity\Stays;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends \Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository<Stays> */
class StaysRepository extends ServiceEntityRepository implements StaysRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stays::class);
    }

    public function findStockById(int $idStay): int
    {
        $stay = $this->createQueryBuilder('s')
            ->andWhere('s.id = :id')
            ->setParameter('id', $idStay)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $stay instanceof Stays ? $stay->getStock() : 0;
    }
}
