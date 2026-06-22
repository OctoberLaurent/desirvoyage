<?php

namespace App\Tests\Controllers;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Contrôle d'accès : les pages réservées redirigent un utilisateur anonyme vers
 * /login (skill §4).
 */
final class AccessControlFunctionalTest extends WebTestCase
{
    #[\Override]
    protected function tearDown(): void
    {
        parent::tearDown();
        static::ensureKernelShutdown();
    }

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
