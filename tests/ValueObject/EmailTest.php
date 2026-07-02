<?php

namespace App\Tests\ValueObject;

use App\ValueObject\Email;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\ValueObject\Email
 */
final class EmailTest extends TestCase
{
    public function testValidEmailConstructsAndExposesValue(): void
    {
        $email = new Email('  Jane@Example.com  ');

        self::assertSame('Jane@Example.com', $email->value());
        self::assertSame('Jane@Example.com', (string) $email);
    }

    public function testInvalidFormatThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Email('not-an-email');
    }

    public function testEmptyThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Email('');
    }

    public function testEqualsIsCaseInsensitive(): void
    {
        self::assertTrue((new Email('a@b.fr'))->equals(new Email('A@B.FR')));
        self::assertFalse((new Email('a@b.fr'))->equals(new Email('a@c.fr')));
    }
}
