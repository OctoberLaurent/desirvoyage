<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * test Main page.
 */
#[\PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses]
class MainPageControllerTest extends WebTestCase
{
    public function testMainPost(): void
    {
        $client = static::createClient();

        $client->request('GET', '/');

        self::assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
