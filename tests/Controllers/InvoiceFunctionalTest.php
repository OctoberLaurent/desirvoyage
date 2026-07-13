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
 * Invoice rendering (skill §4 — covers an untested high-stakes page).
 *
 * Safety net for the Money value object: the invoice displays prices through
 * Twig arithmetic (pre-tax price = price*100/120, nbtravelers*price lines,
 * number_format). This test asserts exact values to detect a silent regression
 * caused by a value object breaking this arithmetic.
 *
 * Authentication uses http_basic (test configuration). The reservation is created
 * in dock_test (stay + travel, option, 2 travelers, price = 1200).
 */
#[\PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses]
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

        // Two travelers (nbtravelers = 2).
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

        // Price including tax ({{ reservation.price }}) renders float 1200.0 as "1200" in Twig.
        self::assertStringContainsString('1200', $content, 'Le prix TTC doit s\'afficher.');

        // Price before tax = including-tax price * 100 / 120 = 1000, formatted as "1,000.00".
        $expectedHt = number_format(self::PRICE_TTC * 100 / 120, 2, '.', ',');
        self::assertStringContainsString($expectedHt, $content, 'Le prix HT (number_format) doit s\'afficher.');

        // VAT = including-tax price - pre-tax price = 200, formatted as "200.00".
        $expectedTva = number_format(self::PRICE_TTC - (self::PRICE_TTC * 100 / 120), 2, '.', ',');
        self::assertStringContainsString($expectedTva, $content, 'La TVA (number_format) doit s\'afficher.');
    }

    /**
     * The PDF route (/invoicepdf/{id}) renders the same template through
     * InvoicePdfGenerator (Dompdf), without controller variables. The template
     * must calculate pre-tax price and VAT itself through Money::amount() to avoid
     * raising "Variable ht does not exist".
     */
    public function testInvoicePdfGeneratesPdf(): void
    {
        $id = $this->createInvoiceReservation();

        $this->client->request('GET', '/invoicepdf/'.$id);

        self::assertResponseIsSuccessful();
        self::assertSame('application/pdf', $this->client->getResponse()->headers->get('Content-Type'));
        // Content-Disposition is inline and uses a .pdf filename.
        self::assertStringContainsString('inline; filename=', (string) $this->client->getResponse()->headers->get('Content-Disposition'));
        self::assertStringContainsString('.pdf', (string) $this->client->getResponse()->headers->get('Content-Disposition'));
    }
}
