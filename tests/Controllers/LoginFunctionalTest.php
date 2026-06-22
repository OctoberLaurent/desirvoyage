<?php

namespace App\Tests\Controllers;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Parcours de connexion (skill §4 — tests fonctionnels au-delà des smoke tests).
 */
final class LoginFunctionalTest extends WebTestCase
{
    #[\Override]
    protected function tearDown(): void
    {
        parent::tearDown();
        static::ensureKernelShutdown();
    }

    public function testValidCredentialsLogInAndRedirectToHome(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        // Le jeton CSRF est un champ caché du formulaire, inclus automatiquement.
        $form = $crawler->selectButton('Connection')->form([
            'email' => 'user@user.fr',
            'password' => '123456',
        ]);
        $client->submit($form);

        // user@user.fr est ROLE_USER → onAuthenticationSuccess redirige vers travel_home ('/')
        self::assertResponseRedirects('/');
    }

    public function testInvalidCredentialsStayOnLoginPage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Connection')->form([
            'email' => 'unknown@example.com',
            'password' => 'wrongpass',
        ]);
        $client->submit($form);

        // Authentification refusée → retour sur /login
        self::assertResponseRedirects('/login');
    }
}
