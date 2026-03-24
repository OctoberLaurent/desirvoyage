<?php

namespace App\Repository;

use App\Entity\Stays;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class StaysRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stays::class);
    }

    public function findStockByid(int $idStay): int
    {
        $stock = $this->createQueryBuilder('s')
            ->andWhere('s.id = :id')
            ->setParameter('id', $idStay)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
        ;

        return $stock[0]->getStock();
    }
}
