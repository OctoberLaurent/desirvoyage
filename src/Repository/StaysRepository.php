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

    /**
     * @return Stays[] Returns an array of Stays objects
     *
     */
    public function findStockByid($idStay)
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

    public function findAllStock()
    {
            return $this->createQueryBuilder('s')
            ->select('SUM(s.stock)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
