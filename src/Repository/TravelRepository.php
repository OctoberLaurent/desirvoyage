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

    public function findTravelsByNameAndPrice($search)
    {
        $qb = $this->createQueryBuilder('t');
        $qb->innerJoin('t.stays', 's')
           ->innerJoin('t.formality', 'f');
        if($search['startdate'] && $search['enddate']){
            $qb->andwhere(
                $qb->expr()->orX(
                        $qb->expr()->andX(
                            $qb->expr()->gt('s.starDate', ':startdate' ),
                            $qb->expr()->lt('s.endDate', ':enddate' ),
                        )
                    )
            )
            ->setParameter('startdate', $search['startdate'])
            ->setParameter('enddate', $search['enddate']);
        }

        if( $search['country'] ){
            $qb->andWhere( $qb->expr()->eq('f.destination', ':country'))
             ->setParameter('country', $search['country']->getDestination());
        }

        if( $search['search'] ){
            $qb->andwhere(
                $qb->expr()->orX(
                       $qb->expr()->like('s.arrival', ':search'),
                       $qb->expr()->like('t.descriptions', ':search'),
                       $qb->expr()->eq('f.destination', ':search'),
                    )
            )
            ->setParameter('search', '%' . $search['search'] . '%' );
            
        
        }

        if( $search['maxprice'] ){
            $qb->andWhere( $qb->expr()->lt('s.price', ':price') )
             ->setParameter('price', floatval($search['maxprice']));
        }

        return $qb->getQuery()->getResult();
    }

}
