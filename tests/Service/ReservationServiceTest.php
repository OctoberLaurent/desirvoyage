<?php

namespace App\Tests\Service;

use App\Entity\Reservation;
use App\Entity\Stay;
use App\Entity\Traveler;
use App\Entity\User;
use App\Repository\StayRepositoryInterface;
use App\Service\MakeSerialService;
use App\Service\ReservationMergeService;
use App\Service\ReservationService;
use App\Service\StockManagementService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

final class ReservationServiceTest extends TestCase
{
    public function testValidationReservesStockAndPersistsInsideOneTransaction(): void
    {
        $stay = new Stay();
        $stay->setStock(2);
        $this->setEntityId($stay, 10);
        $user = new User();
        $this->setEntityId($user, 20);

        $reservation = new Reservation();
        $reservation->addStay($stay);
        $reservation->addTraveler(new Traveler());
        $reservation->setUser($user);

        $stayRepository = $this->createMock(StayRepositoryInterface::class);
        $stayRepository->expects(self::once())->method('findForUpdate')->with(10)->willReturn($stay);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects(self::once())->method('wrapInTransaction')
            ->willReturnCallback(static fn (callable $callback): mixed => $callback($entityManager));
        $entityManager->method('find')->willReturnCallback(
            static fn (string $class): ?object => match ($class) {
                User::class => $user,
                Stay::class => $stay,
                default => null,
            },
        );
        $entityManager->expects(self::once())->method('persist')->with($reservation);
        $entityManager->expects(self::never())->method('flush');

        $service = new ReservationService(
            new MakeSerialService(),
            new StockManagementService($stayRepository),
            new ReservationMergeService($entityManager),
            $entityManager,
        );

        self::assertSame($reservation, $service->validate($reservation));
        self::assertSame(1, $stay->getStock());
        self::assertInstanceOf(\DateTime::class, $reservation->getCreatedDate());
    }

    public function testUpdatingReservationUsesMutableDateExpectedByDoctrineDatetime(): void
    {
        $stay = new Stay();
        $this->setEntityId($stay, 10);
        $user = new User();
        $this->setEntityId($user, 20);

        $reservation = new Reservation();
        $reservation->addStay($stay);
        $reservation->setUser($user);
        $reservation->setCreatedDate(new \DateTime('-1 day'));

        $stayRepository = $this->createMock(StayRepositoryInterface::class);
        $stayRepository->expects(self::never())->method('findForUpdate');
        $entityManager = self::createStub(EntityManagerInterface::class);
        $entityManager->method('wrapInTransaction')
            ->willReturnCallback(static fn (callable $callback): mixed => $callback($entityManager));
        $entityManager->method('find')->willReturnCallback(
            static fn (string $class): ?object => match ($class) {
                User::class => $user,
                Stay::class => $stay,
                default => null,
            },
        );

        $service = new ReservationService(
            new MakeSerialService(),
            new StockManagementService($stayRepository),
            new ReservationMergeService($entityManager),
            $entityManager,
        );

        $service->validate($reservation);

        self::assertInstanceOf(\DateTime::class, $reservation->getUpdatedAt());
    }

    private function setEntityId(object $entity, int $id): void
    {
        $property = new \ReflectionProperty($entity, 'id');
        $property->setValue($entity, $id);
    }
}
