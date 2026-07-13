<?php

namespace App\Tests\Controllers;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Contact page (skill §4 — DTO form rendering smoke test).
 */
#[\PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses]
final class ContactFunctionalTest extends WebTestCase
{
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
