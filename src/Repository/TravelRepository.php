<?php

namespace App\Repository;

use App\Entity\Formality;
use App\Entity\Travel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends \Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository<Travel> */
class TravelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Travel::class);
    }

    /**
     * @param array<string, mixed> $search
     *
     * @return array<int, Travel>
     */
    public function findTravelsByNameAndPrice(array $search): array
    {
        $qb = $this->createQueryBuilder('t');
        $qb->innerJoin('t.stays', 's')
           ->innerJoin('t.formality', 'f');
        $startdate = $search['startdate'] ?? null;
        $enddate = $search['enddate'] ?? null;
        if (null !== $startdate && null !== $enddate) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->andX(
                        $qb->expr()->gt('s.starDate', ':startdate'),
                        $qb->expr()->lt('s.endDate', ':enddate'),
                    )
                )
            )
            ->setParameter('startdate', $startdate)
            ->setParameter('enddate', $enddate);
        }

        $country = $search['country'] ?? null;
        if ($country instanceof Formality) {
            $qb->andWhere($qb->expr()->eq('f.destination', ':country'))
             ->setParameter('country', $country->getDestination());
        }

        $term = $search['search'] ?? null;
        if (is_string($term) && '' !== $term) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('s.arrival', ':search'),
                    $qb->expr()->like('t.descriptions', ':search'),
                    $qb->expr()->eq('f.destination', ':search'),
                )
            )
            ->setParameter('search', '%'.$term.'%');
        }

        $maxprice = $search['maxprice'] ?? null;
        if (is_numeric($maxprice)) {
            $qb->andWhere($qb->expr()->lt('s.price', ':price'))
             ->setParameter('price', (float) $maxprice);
        }

        /** @var array<int, Travel> $result */
        $result = $qb->getQuery()->getResult();

        return $result;
    }
}
