<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class PageControllerTest extends WebTestCase
{
    private $client;

    /**
     * SetUp Authenticate.
     */
    public function setUp(): void
    {
        $this->client = static::createClient([], [
            'PHP_AUTH_USER' => 'user@user.fr',
            'PHP_AUTH_PW' => '123456',
        ]);
    }

    /**
     * Simulate login and stock session.
     */
    private function logIn()
    {
        $session = $this->client->getContainer()->get('session');

        $firewallName = 'main';
        $user = new \App\Entity\User();
        $user->setEmail('user@user.fr');
        $user->setRoles(['ROLE_USER']);
        $token = new UsernamePasswordToken($user, $firewallName, $user->getRoles());
        $session->set('_security_'.$firewallName, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }

    /**
     * Test secured page.
     */
    public function testSecuredPageadmin()
    {
        $this->logIn();
        $this->client->request('GET', '/reservation/list');
        self::assertSame(301, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Test secured page.
     */
    public function testSecuredPageGift()
    {
        $this->logIn();
        $this->client->request('GET', '/profil/dashboard');
        self::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }
}
