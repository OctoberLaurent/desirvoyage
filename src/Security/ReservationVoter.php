<?php

namespace App\Security;

use App\Entity\Reservation;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/** @phpstan-extends Voter<'RESERVATION_VIEW'|'RESERVATION_PAY', mixed> */
final class ReservationVoter extends Voter
{
    public const VIEW = 'RESERVATION_VIEW';
    public const PAY = 'RESERVATION_PAY';

    #[\Override]
    protected function supports(string $attribute, mixed $subject): bool
    {
        return $subject instanceof Reservation && in_array($attribute, [self::VIEW, self::PAY], true);
    }

    #[\Override]
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        if (!$subject instanceof Reservation) {
            return false;
        }

        $user = $token->getUser();

        return $user instanceof User && $subject->belongsTo($user);
    }
}
