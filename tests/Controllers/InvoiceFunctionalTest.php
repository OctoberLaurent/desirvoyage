<?php

namespace App\Tests\Controllers;

use App\Entity\Option;
use App\Entity\Reservation;
use App\Entity\Stay;
use App\Entity\Traveler;
use App\Entity\User;
use App\ValueObject\Email;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Rendu de la facture (skill §4 — couvre la page high-stakes non testée).
 *
 * Filet de sécurité pour la future introduction du VO Money : la facture
 * affiche des prix via arithmétique Twig (HT = price*100/120, lignes
 * nbtravelers*price, number_format). Ce test asserte les valeurs exactes
 * pour qu'une régression silencieuse (VO Money cassant l'arithmétique) soit
 * détectée.
 *
 * Authentification via http_basic (config test). La réservation est créée
 * dans dock_test (stay+travel, option, 2 travelers, price=1200).
 */
final class InvoiceFunctionalTest extends WebTestCase
{
    private KernelBrowser $client;
    private ObjectManager $em;

    private const PRICE_TTC = 1200.0;

    #[\Override]
    protected function setUp(): void
    {
        $this->client = static::createClient([], [
            'PHP_AUTH_USER' => 'user@user.fr',
            'PHP_AUTH_PW' => '123456',
        ]);
        $this->em = static::getContainer()->get('doctrine')->getManager();
    }

    #[\Override]
    protected function tearDown(): void
    {
        parent::tearDown();
        static::ensureKernelShutdown();
    }

    private function createInvoiceReservation(): int
    {
        $stay = $this->em->getRepository(Stay::class)->findOneBy([], ['id' => 'ASC']);
        self::assertNotNull($stay, 'Fixtures: au moins un Stay requis (lancer `make test-db`).');
        $option = $this->em->getRepository(Option::class)->findOneBy([], ['id' => 'ASC']);
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => 'user@user.fr']);
        self::assertNotNull($user, 'Fixtures: user@user.fr requis (lancer `make test-db`).');

        $reservation = new Reservation();
        $reservation->setSerial('TEST-'.uniqid());
        $reservation->setUser($user);
        $reservation->addStay($stay);
        if (null !== $option) {
            $reservation->addOption($option);
        }

        // Deux voyageurs (nbtravelers = 2).
        for ($i = 1; $i <= 2; ++$i) {
            $traveler = new Traveler();
            $traveler->setLastname('Nom'.$i);
            $traveler->setFirstname('Prenom'.$i);
            $traveler->setEmail(new Email('voyageur'.$i.'@example.com'));
            $traveler->setBirthday(new \DateTime('1990-01-15'));
            $reservation->addTraveler($traveler);
        }

        $reservation->setPrice(self::PRICE_TTC);

        $this->em->persist($reservation);
        $this->em->flush();

        return (int) $reservation->getId();
    }

    public function testInvoiceHtmlRendersAndDisplaysPrices(): void
    {
        $id = $this->createInvoiceReservation();

        $this->client->request('GET', '/invoice/'.$id);

        self::assertResponseIsSuccessful();
        $content = (string) $this->client->getResponse()->getContent();

        // Prix TTC ({{ reservation.price }}) — Twig rend le float 1200.0 → « 1200 ».
        self::assertStringContainsString('1200', $content, 'Le prix TTC doit s\'afficher.');

        // Prix HT = TTC * 100 / 120 = 1000 → number_format(2) → « 1,000.00 ».
        $expectedHt = number_format(self::PRICE_TTC * 100 / 120, 2, '.', ',');
        self::assertStringContainsString($expectedHt, $content, 'Le prix HT (number_format) doit s\'afficher.');

        // TVA = TTC - HT = 200 → number_format(2) → « 200.00 ».
        $expectedTva = number_format(self::PRICE_TTC - (self::PRICE_TTC * 100 / 120), 2, '.', ',');
        self::assertStringContainsString($expectedTva, $content, 'La TVA (number_format) doit s\'afficher.');
    }

    /**
     * La route PDF (/invoicepdf/{id}) rend le MÊME template via InvoicePdfGenerator
     * (Dompdf) — sans passer de vars contrôleur. Le template doit donc calculer
     * ht/tva lui-même (VO Money via .amount()) pour ne pas lever « Variable ht does not exist ».
     */
    public function testInvoicePdfGeneratesPdf(): void
    {
        $id = $this->createInvoiceReservation();

        $this->client->request('GET', '/invoicepdf/'.$id);

        self::assertResponseIsSuccessful();
        self::assertSame('application/pdf', $this->client->getResponse()->headers->get('Content-Type'));
        // Content-Disposition inline avec un nom de fichier .pdf
        self::assertStringContainsString('inline; filename=', (string) $this->client->getResponse()->headers->get('Content-Disposition'));
        self::assertStringContainsString('.pdf', (string) $this->client->getResponse()->headers->get('Content-Disposition'));
    }
}
