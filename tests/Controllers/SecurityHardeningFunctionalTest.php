<?php

namespace App\Tests\Controllers;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

#[\PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses]
final class SecurityHardeningFunctionalTest extends WebTestCase
{
    public function testForgottenPasswordRejectsPostWithoutCsrfToken(): void
    {
        $client = static::createClient();

        $client->request('POST', '/mot-de-passe-oublie', [
            'email' => 'user@user.fr',
        ]);

        self::assertResponseStatusCodeSame(400);
    }
}
