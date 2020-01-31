<?php

namespace App\Repository;

use App\Entity\Travel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Travel|null find($id, $lockMode = null, $lockVersion = null)
 * @method Travel|null findOneBy(array $criteria, array $orderBy = null)
 * @method Travel[]    findAll()
 * @method Travel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TravelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Travel::class);
    }

    public function findTravelsByNameAndPrice($price , $search, $sdate, $edate)
    {
        $qb = $this->createQueryBuilder('t');

        return $qb->innerJoin('t.stays', 's')
            ->innerJoin('t.formality', 'f')
            ->where(
                $qb->expr()->orX(
                        $qb->expr()->andX(
                            $qb->expr()->gt('s.starDate', ':startdate' ),
                            $qb->expr()->lt('s.endDate', ':enddate' ),
                            $gqb->expr()->lt('s.price', ':price'),
                            
                        ),
                        $qb->expr()->orX(
                            $qb->expr()->like('s.arrival', $qb->expr()->literal('%:search%')),
                            $qb->expr()->eq('f.destination', ':search'),
                        )
                    )
            )
            ->setParameter('price', $price)
            ->setParameter('startdate', $sdate)
            ->setParameter('enddate', $edate)
            ->setParameter('search', $search)
            ->getQuery()
            ->getResult()
        ;
    }

}
