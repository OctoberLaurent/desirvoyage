<?php

namespace App\Tests\Controllers;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Access control: protected pages redirect anonymous users to /login (skill §4).
 */
#[\PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses]
final class AccessControlFunctionalTest extends WebTestCase
{
    public function testReservationRedirectsAnonymousToLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/reservation/list/');

        // #[IsGranted('ROLE_USER')] on ReservationController → 302 to /login.
        self::assertResponseRedirects('/login');
    }

    public function testProfilDashboardRedirectsAnonymousToLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/profil/dashboard');

        self::assertResponseRedirects('/login');
    }
}
