<?php

namespace App\Repository;

use App\Entity\Stays;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Stays|null find($id, $lockMode = null, $lockVersion = null)
 * @method Stays|null findOneBy(array $criteria, array $orderBy = null)
 * @method Stays[]    findAll()
 * @method Stays[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StaysRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stays::class);
    }

    // /**
    //  * @return Stays[] Returns an array of Stays objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Stays
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
