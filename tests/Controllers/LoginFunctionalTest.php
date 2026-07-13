<?php

namespace App\Tests\Controllers;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Login flow (skill §4 — functional tests beyond smoke tests).
 */
#[\PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses]
final class LoginFunctionalTest extends WebTestCase
{
    public function testValidCredentialsLogInAndRedirectToHome(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        // The CSRF token is a hidden form field and is included automatically.
        $form = $crawler->selectButton('Connection')->form([
            'email' => 'user@user.fr',
            'password' => '123456',
        ]);
        $client->submit($form);

        // user@user.fr has ROLE_USER, so onAuthenticationSuccess redirects to travel_home ('/').
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

        // Authentication is rejected, so the request returns to /login.
        self::assertResponseRedirects('/login');
    }
}
