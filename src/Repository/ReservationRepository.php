<?php

namespace App\Repository;

use App\Entity\Reservation;
use App\Enum\ReservationStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\LockMode;
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
    #[\Override]
    public function findUnpaid(): array
    {
        return $this->findBy(['payment' => null]);
    }

    #[\Override]
    public function findExpiredPending(\DateTimeImmutable $createdBefore): array
    {
        /** @var array<int, Reservation> $reservations */
        $reservations = $this->createQueryBuilder('reservation')
            ->andWhere('reservation.status = :status')
            ->andWhere('reservation.createdDate < :createdBefore')
            ->setParameter('status', ReservationStatus::Pending)
            ->setParameter('createdBefore', $createdBefore)
            ->getQuery()
            ->setLockMode(LockMode::PESSIMISTIC_WRITE)
            ->getResult();

        return $reservations;
    }
}
