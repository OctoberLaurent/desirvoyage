<?php

namespace App\Tests\Security;

use App\Entity\Reservation;
use App\Entity\User;
use App\Security\ReservationVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

final class ReservationVoterTest extends TestCase
{
    public function testOwnerCanViewAndPayTheirReservation(): void
    {
        $owner = new User();
        $reservation = (new Reservation())->setUser($owner);
        $token = $this->tokenFor($owner);
        $voter = new ReservationVoter();

        self::assertSame(VoterInterface::ACCESS_GRANTED, $voter->vote($token, $reservation, [ReservationVoter::VIEW]));
        self::assertSame(VoterInterface::ACCESS_GRANTED, $voter->vote($token, $reservation, [ReservationVoter::PAY]));
    }

    public function testAnotherUserCannotViewOrPayAReservation(): void
    {
        $reservation = (new Reservation())->setUser(new User());
        $voter = new ReservationVoter();

        self::assertSame(VoterInterface::ACCESS_DENIED, $voter->vote($this->tokenFor(new User()), $reservation, [ReservationVoter::VIEW]));
        self::assertSame(VoterInterface::ACCESS_DENIED, $voter->vote($this->tokenFor(new User()), $reservation, [ReservationVoter::PAY]));
    }

    private function tokenFor(User $user): TokenInterface
    {
        $token = self::createStub(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        return $token;
    }
}
