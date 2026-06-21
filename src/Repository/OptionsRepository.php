<?php

namespace App\Repository;

use App\Entity\Options;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends \Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository<Options> */
class OptionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Options::class);
    }

    public function findOptions(int $travelId): \Doctrine\ORM\QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.travels', 't')
            ->where('t.id = :travelId')
            ->setParameter('travelId', $travelId)
        ;
    }

    // SELECT * FROM `travel_options` WHERE travel_id = 253

    /*
    public function findOneBySomeField($value): ?Options
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
