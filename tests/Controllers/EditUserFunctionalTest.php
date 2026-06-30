<?php

namespace App\Tests\Controllers;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Édition du profil (skill §4 — couvre la refacto EditUserType sur DTO, sinon
 * non testée). Authentification via http_basic (config test).
 */
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

    #[\Override]
    protected function tearDown(): void
    {
        parent::tearDown();
        static::ensureKernelShutdown();
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
        // Re-soumet le formulaire pré-rempli avec les valeurs actuelles du user
        // (email inchangé = user@user.fr → pas de conflit UniqueEntity).
        $form = $crawler->selectButton('Valider')->form();
        $this->client->submit($form);

        self::assertResponseRedirects('/profil/dashboard');
    }
}
