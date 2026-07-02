<?php

namespace App\Tests\Controllers;

use App\Entity\Stay;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Parcours de configuration d'une réservation (skill §4 — couvre le flux
 * multi-étapes session-based non testé : index → configure options →
 * configureTravelers → summary).
 *
 * Authentification via http_basic (config test). DB de test (dock_test) avec
 * fixtures (user@user.fr + TravelFixtures : stays avec stock).
 *
 * Ce filet de test protège la future refonte TravelerDto (skill §8) et valide
 * le VO Email sur Traveler (getEmail(): Email + setEmail(Email|string)) bout-en-bout.
 */
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

    #[\Override]
    protected function tearDown(): void
    {
        parent::tearDown();
        static::ensureKernelShutdown();
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
     * Soumet le formulaire d'options via POST brut (le template configureOption a
     * un <div> ouvert avant <form> qui désolidarise les champs du <form> au parsing
     * libxml du DomCrawler ; en navigateur réel le HTML5 parser tolère).
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
     * Étape 1 : /reservation?stayid= démarre une réservation (rendu 200).
     */
    public function testIndexStartsReservation(): void
    {
        $id = $this->stayId();
        $this->client->request('GET', '/reservation?stayid='.$id);

        self::assertResponseIsSuccessful();
    }

    /**
     * Étapes 1→2 : démarrage puis rendu du formulaire d'options.
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
     * Étape 2 : soumission du formulaire d'options (aucune option = valide)
     * → avance vers les voyageurs.
     */
    public function testConfigureOptionsSubmitAdvancesToTravelers(): void
    {
        $id = $this->stayId();
        $this->client->request('GET', '/reservation?stayid='.$id);
        $this->submitOptionsForm($id);

        self::assertResponseRedirects('/reservation/configure/configureTravelers/'.$id);
    }

    /**
     * Étape 4 (garde-fou) : summary sans voyageur → redirection vers les voyageurs.
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
     * Étapes 3→4 : soumission des voyageurs (CollectionType + allow_add) puis
     * rendu du récapitulatif. Valide le VO Email sur Traveler bout-en-bout :
     *  - le formulaire écrit une string via setEmail(Email|string) (union) ;
     *  - le template summary affiche traveler.email via getEmail(): Email → __toString.
     *
     * Note : summary recharge les stays en entités managées (refreshStays) afin
     * que le proxy Travel puisse lazy-loader après désérialisation session.
     */
    public function testConfigureTravelersSubmitReachesSummaryAndDisplaysEmail(): void
    {
        $id = $this->stayId();
        $this->client->request('GET', '/reservation?stayid='.$id);
        $this->submitOptionsForm($id);

        // Rendu du formulaire voyageurs pour récupérer le jeton CSRF.
        $crawler = $this->client->request('GET', '/reservation/configure/configureTravelers/'.$id);
        self::assertResponseIsSuccessful();

        $tokenNode = $crawler->filter('input[name="travelers[_token]"]');
        self::assertGreaterThan(0, $tokenNode->count(), 'Le champ CSRF travelers[_token] doit être rendu.');
        $csrfToken = $tokenNode->attr('value');

        // Soumission d'un voyageur via POST brut (CollectionType allow_add : les
        // entrées ne sont pas rendues dans le DOM, seul le prototype l'est).
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
        // L'e-mail du voyageur est affiché (table voyageurs) via Email::__toString.
        self::assertStringContainsString('jane.doe@example.com', (string) $this->client->getResponse()->getContent());
    }
}
