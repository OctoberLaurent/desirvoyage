<?php

namespace App\Tests\Controllers;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Page de contact (skill §4 — smoke test du rendu du formulaire sur DTO).
 */
final class ContactFunctionalTest extends WebTestCase
{
    #[\Override]
    protected function tearDown(): void
    {
        parent::tearDown();
        static::ensureKernelShutdown();
    }

    public function testContactPageRendersForm(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/contact');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('form');
        self::assertSelectorExists('input[name="contact[lastname]"]');
        self::assertSelectorExists('input[name="contact[email]"]');
        self::assertSelectorExists('textarea[name="contact[description]"]');
        self::assertSelectorExists('button[type=submit]');
    }
}
