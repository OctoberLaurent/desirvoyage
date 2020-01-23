<?php

namespace App\Repository;

use App\Entity\Formality;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Formality|null find($id, $lockMode = null, $lockVersion = null)
 * @method Formality|null findOneBy(array $criteria, array $orderBy = null)
 * @method Formality[]    findAll()
 * @method Formality[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormalityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Formality::class);
    }

    // /**
    //  * @return Formality[] Returns an array of Formality objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Formality
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
