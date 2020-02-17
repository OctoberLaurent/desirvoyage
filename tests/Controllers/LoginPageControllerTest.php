<?php 

namespace App\Tests\Controllers;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Test Login Page 
 * 
 */
class LoginPageControllerTest extends WebTestCase
{
    public function testLoginPage()
    {
    $client = static::createClient();
    $crawler = $client->request('GET', '/login');
    $form = $crawler->selectButton('Connection')->form([
        'email' => 'test@test.fr',
        'password' => '123456'
    ]);
    $client->submit($form);
    $this->assertResponseRedirects('/');
    $client->followRedirect();
    //$this->assertSelectorExists('.card');
    }
}