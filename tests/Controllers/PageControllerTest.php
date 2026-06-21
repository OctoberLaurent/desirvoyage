<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class PageControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    /**
     * SetUp Authenticate.
     */
    #[\Override]
    public function setUp(): void
    {
        $this->client = static::createClient([], [
            'PHP_AUTH_USER' => 'user@user.fr',
            'PHP_AUTH_PW' => '123456',
        ]);
    }

    /**
     * Test secured page.
     */
    public function testSecuredPageadmin(): void
    {
        $this->client->request('GET', '/reservation/list');
        self::assertSame(301, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Test secured page.
     */
    public function testSecuredPageGift(): void
    {
        $this->client->request('GET', '/profil/dashboard');
        self::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }
}
