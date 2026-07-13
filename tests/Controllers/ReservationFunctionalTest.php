<?php

namespace App\Tests\Controllers;

use App\Entity\Stay;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Reservation configuration flow (skill §4 — covers the untested session-based
 * multi-step flow: index → configure options →
 * configureTravelers → summary).
 *
 * Authentication uses http_basic (test configuration). The dock_test database
 * has fixtures (user@user.fr + TravelFixtures: stays with stock).
 *
 * This safety net protects the TravelerDto refactor (skill §8) and validates the
 * Email value object on Traveler (getEmail(): Email + setEmail(Email|string)) end to end.
 */
#[\PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses]
final class ReservationFunctionalTest extends WebTestCase
{
    private KernelBrowser $client;

    #[\Override]
    protected function setUp(): void
    {
        $this->client = static::createClient([], [
            'PHP_AUTH_USER' => 'user@user.fr',
            'PHP_AUTH_PW' => '123456',
        ]);
    }

    private function stayId(): int
    {
        $stay = static::getContainer()->get('doctrine')
            ->getRepository(Stay::class)
            ->findOneBy([], ['id' => 'ASC']);

        self::assertNotNull($stay, 'Le jeu de fixtures doit contenir au moins un Stay (lancer `make test-db`).');

        return (int) $stay->getId();
    }

    /**
     * Submits the options form through a raw POST. The configureOption template
     * opens a <div> before <form>, which detaches fields from the form when
     * DomCrawler uses libxml parsing; a real browser's HTML5 parser tolerates it.
     */
    private function submitOptionsForm(int $id): void
    {
        $crawler = $this->client->request('GET', '/reservation/configure/'.$id);
        $token = $crawler->filter('input[name="reservation_option[_token]"]')->attr('value');

        $this->client->request('POST', '/reservation/configure/'.$id, [
            'reservation_option' => [
                'options' => [],
                '_token' => $token,
            ],
        ]);
    }

    /**
     * Step 1: /reservation?stayid= starts a reservation (200 response).
     */
    public function testIndexStartsReservation(): void
    {
        $id = $this->stayId();
        $this->client->request('GET', '/reservation?stayid='.$id);

        self::assertResponseIsSuccessful();
    }

    /**
     * Steps 1→2: starts a reservation, then renders the options form.
     */
    public function testConfigureOptionsFormRenders(): void
    {
        $id = $this->stayId();
        $this->client->request('GET', '/reservation?stayid='.$id);
        $this->client->request('GET', '/reservation/configure/'.$id);

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('form[name="reservation_option"]');
        self::assertSelectorExists('input[name="reservation_option[options][]"]');
    }

    /**
     * Step 2: submits the options form (no option is valid) and advances to
     * travelers.
     */
    public function testConfigureOptionsSubmitAdvancesToTravelers(): void
    {
        $id = $this->stayId();
        $this->client->request('GET', '/reservation?stayid='.$id);
        $this->submitOptionsForm($id);

        self::assertResponseRedirects('/reservation/configure/configureTravelers/'.$id);
    }

    /**
     * Step 4 safety net: a summary without travelers redirects to travelers.
     */
    public function testSummaryWithoutTravelersRedirectsToTraveler(): void
    {
        $id = $this->stayId();
        $this->client->request('GET', '/reservation?stayid='.$id);
        $this->submitOptionsForm($id);

        $this->client->request('GET', '/reservation/summary');

        self::assertResponseRedirects('/reservation/configure/configureTravelers/'.$id);
    }

    /**
     * Steps 3→4: submits travelers (CollectionType + allow_add) and renders the
     * summary. Validates the Email value object on Traveler end to end:
     *  - the form writes a string through setEmail(Email|string) (union);
     *  - the summary template displays traveler.email through getEmail(): Email → __toString.
     *
     * Note: summary reloads stays as managed entities through refreshStays so the
     * Travel proxy can lazy-load after session deserialization.
     */
    public function testConfigureTravelersSubmitReachesSummaryAndDisplaysEmail(): void
    {
        $id = $this->stayId();
        $this->client->request('GET', '/reservation?stayid='.$id);
        $this->submitOptionsForm($id);

        // Render the traveler form to retrieve the CSRF token.
        $crawler = $this->client->request('GET', '/reservation/configure/configureTravelers/'.$id);
        self::assertResponseIsSuccessful();

        $tokenNode = $crawler->filter('input[name="travelers[_token]"]');
        self::assertGreaterThan(0, $tokenNode->count(), 'Le champ CSRF travelers[_token] doit être rendu.');
        $csrfToken = $tokenNode->attr('value');

        $addBuyerButton = $crawler->filter('#add-user-in-traveler');
        self::assertCount(1, $addBuyerButton);
        self::assertSame('user', $addBuyerButton->attr('data-buyer-lastname'));
        self::assertSame('user', $addBuyerButton->attr('data-buyer-firstname'));
        self::assertSame('user@user.fr', $addBuyerButton->attr('data-buyer-email'));
        self::assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', (string) $addBuyerButton->attr('data-buyer-birthday'));

        // Submit a traveler through a raw POST: CollectionType allow_add entries
        // are not rendered in the DOM; only the prototype is rendered.
        $this->client->request('POST', '/reservation/configure/configureTravelers/'.$id, [
            'travelers' => [
                'travelers' => [
                    [
                        'lastname' => 'Doe',
                        'firstname' => 'Jane',
                        'email' => 'jane.doe@example.com',
                        'birthday' => '1990-05-15',
                    ],
                ],
                '_token' => $csrfToken,
            ],
        ]);

        self::assertResponseRedirects('/reservation/summary');

        $this->client->request('GET', '/reservation/summary');
        self::assertResponseIsSuccessful();
        // The traveler email is displayed in the travelers table through Email::__toString().
        self::assertStringContainsString('jane.doe@example.com', (string) $this->client->getResponse()->getContent());
        self::assertCount(1, $this->client->getCrawler()->filter('.reservation-summary-actions'));
        self::assertCount(3, $this->client->getCrawler()->filter('.reservation-summary-actions > *'));
    }

    public function testSummaryModalKeepsThePaymentActionVisible(): void
    {
        $id = $this->stayId();
        $this->client->request('GET', '/reservation?stayid='.$id);
        $this->submitOptionsForm($id);

        $crawler = $this->client->request('GET', '/reservation/configure/configureTravelers/'.$id);
        $csrfToken = $crawler->filter('input[name="travelers[_token]"]')->attr('value');
        $this->client->request('POST', '/reservation/configure/configureTravelers/'.$id, [
            'travelers' => [
                'travelers' => [[
                    'lastname' => 'Doe',
                    'firstname' => 'Jane',
                    'email' => 'jane.modal@example.com',
                    'birthday' => '1990-05-15',
                ]],
                '_token' => $csrfToken,
            ],
        ]);

        $crawler = $this->client->request('GET', '/reservation/summary');

        self::assertResponseIsSuccessful();
        self::assertCount(1, $crawler->filter('#modal-valid form.reservation-validation-form'));
        self::assertCount(1, $crawler->filter('#modal-valid form.reservation-validation-form button[type="submit"]'));
    }

    public function testValidatePersistsReservationAndRedirectsToPayment(): void
    {
        $id = $this->stayId();
        $this->client->request('GET', '/reservation?stayid='.$id);
        $this->submitOptionsForm($id);

        $crawler = $this->client->request('GET', '/reservation/configure/configureTravelers/'.$id);
        $csrfToken = $crawler->filter('input[name="travelers[_token]"]')->attr('value');
        $this->client->request('POST', '/reservation/configure/configureTravelers/'.$id, [
            'travelers' => [
                'travelers' => [[
                    'lastname' => 'Doe',
                    'firstname' => 'Jane',
                    'email' => 'jane.validate@example.com',
                    'birthday' => '1990-05-15',
                ]],
                '_token' => $csrfToken,
            ],
        ]);

        $crawler = $this->client->request('GET', '/reservation/summary');
        self::assertResponseIsSuccessful();

        $validationToken = $crawler->filter('form[action="/reservation/validate/"] input[name="_token"]')->attr('value');
        $this->client->request('POST', '/reservation/validate/', [
            '_token' => $validationToken,
        ]);

        self::assertResponseRedirects();
        $location = (string) $this->client->getResponse()->headers->get('Location');
        self::assertMatchesRegularExpression('#^/payment/\d+$#', $location);
    }

    public function testStateChangingReservationEndpointsRejectGetRequests(): void
    {
        $this->client->request('GET', '/reservation/remove/');

        self::assertResponseStatusCodeSame(405);

        $id = $this->stayId();
        $this->client->request('GET', '/reservation?stayid='.$id);
        $this->submitOptionsForm($id);
        $crawler = $this->client->request('GET', '/reservation/configure/configureTravelers/'.$id);
        $csrfToken = $crawler->filter('input[name="travelers[_token]"]')->attr('value');
        $this->client->request('POST', '/reservation/configure/configureTravelers/'.$id, [
            'travelers' => [
                'travelers' => [[
                    'lastname' => 'Doe',
                    'firstname' => 'Jane',
                    'email' => 'jane.security@example.com',
                    'birthday' => '1990-05-15',
                ]],
                '_token' => $csrfToken,
            ],
        ]);

        $crawler = $this->client->request('GET', '/reservation/summary');
        self::assertResponseIsSuccessful();
        $validationToken = $crawler->filter('form[action="/reservation/validate/"] input[name="_token"]')->attr('value');
        self::assertNotSame('', $validationToken);

        $this->client->request('POST', '/reservation/validate/', [
            '_token' => $validationToken,
        ]);

        self::assertResponseRedirects();
    }
}
