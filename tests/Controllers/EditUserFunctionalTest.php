<?php

namespace App\Tests\Controllers;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Profile editing (skill §4 — covers the EditUserType DTO refactor, otherwise
 * untested). Authentication uses http_basic (test configuration).
 */
#[\PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses]
final class EditUserFunctionalTest extends WebTestCase
{
    private KernelBrowser $client;

    #[\Override]
    protected function setUp(): void
    {
        $this->client = static::createClient([], [
            'PHP_AUTH_USER' => 'user@user.fr',
            'PHP_AUTH_PW' => '123456',
        ]);
    }

    public function testProfilEditFormRenders(): void
    {
        $this->client->request('GET', '/profil/edit/');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('form');
        self::assertSelectorExists('input[name="edit_user[lastname]"]');
        self::assertSelectorExists('input[name="edit_user[email]"]');
        self::assertSelectorExists('button[type=submit]');
    }

    public function testProfilEditSubmitRedirectsToDashboard(): void
    {
        $crawler = $this->client->request('GET', '/profil/edit/');
        // Resubmits the pre-filled form with the user's current values.
        // The unchanged email (user@user.fr) does not cause a UniqueEntity conflict.
        $form = $crawler->selectButton('Valider')->form();
        $this->client->submit($form);

        self::assertResponseRedirects('/profil/dashboard');
    }
}
