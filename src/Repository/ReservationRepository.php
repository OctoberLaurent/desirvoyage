<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends \Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository<Reservation> */
class ReservationRepository extends ServiceEntityRepository implements ReservationRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    // /**
    //  * @return Reservation[] Returns an array of Reservation objects
    //  */
    /**
     * @return array<int, Reservation>
     */
    public function findUnpaid(): array
    {
        return $this->findBy(['payment' => null]);
    }
}
