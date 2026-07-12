<?php

namespace App\Tests\Controllers;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Contrôle d'accès : les pages réservées redirigent un utilisateur anonyme vers
 * /login (skill §4).
 */
#[\PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses]
final class AccessControlFunctionalTest extends WebTestCase
{
    public function testReservationRedirectsAnonymousToLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/reservation/list/');

        // #[IsGranted('ROLE_USER')] sur ReservationController → 302 vers /login
        self::assertResponseRedirects('/login');
    }

    public function testProfilDashboardRedirectsAnonymousToLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/profil/dashboard');

        self::assertResponseRedirects('/login');
    }
}
