<?php

namespace App\Service;

/**
 * Generates a readable alphanumeric serial number in the `XXX-XXX-XXX` format.
 * Uses cryptographically secure random_int() rather than rand().
 */
final class MakeSerialService
{
    private const array ALPHABET = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
    private const int LENGTH = 9;
    private const int GROUP_SIZE = 3;

    public function makeSerial(): string
    {
        $chars = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, ...self::ALPHABET];
        $max = count($chars) - 1;
        $serial = '';

        for ($i = 0; $i < self::LENGTH; ++$i) {
            if ($i > 0 && ($i % self::GROUP_SIZE) === 0) {
                $serial .= '-';
            }
            $serial .= $chars[random_int(0, $max)];
        }

        return $serial;
    }
}
