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
    $form = $crawler->selectButton('Connexion')->form([
        'email' => 'estelle.denis@example.com',
        'password' => '123456'
    ]);
    $client->submit($form);
    $this->assertResponseRedirects('/admin/dashboard');
    $client->followRedirect();
    //$this->assertSelectorExists('.card-panel .red');
    }
}