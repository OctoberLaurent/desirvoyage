<?php
namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginTest extends WebTestCase
{
    public function testLogin()
    {
        
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $buttonCrawlerNode = $crawler->selectButton('submit');

        $form = $buttonCrawlerNode->form();

        // dd($form);

        $form = $buttonCrawlerNode->form([
            'email'    => 'estelle.denis@example.com',
            'password' => '123456',
        ]);

        $client->submit($form);
        $this->assertResponseRedirects('admin/dashboard');
        $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        
    }
}