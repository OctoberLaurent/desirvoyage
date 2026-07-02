# Task — Conformité au skill `symfony-php-clean-code`

Suivi en temps réel de l'état des tâches. Source de vérité = le code (pas le plan détaillé `docs/refactoring-tasks.md` qui est plus verbeux).

Légende : `[x]` fait · `[~]` partiellement · `[ ]` à faire.

**État global : `make qa` vert** (PHPStan level 10 = 0 erreur, PHP-CS-Fixer = 0, PHPUnit **25 tests / 73 assertions**, Cypress **11/11**, app HTTP 200, dev DB `dock` intacte).

---

## P0 — Bugs, sécurité, quick wins

- [x] **P0-1** `IncrementalStaysNotPurchasedCommand`
  - [x] bug `$seats .= → +=` · `dump()` retiré · garde `getCreatedDate()===null` · `#[AsCommand]` · promoted readonly · `final`
  - [x] extraction `ExpiredReservationCleanupService` (la commande ne fait que l'orchestration console)
- [x] **P0-2** `PaymentController` (sécurité Stripe)
  - [x] clé privée retirée du template Twig · `$_ENV` → paramètres `stripe.*`
  - [x] extraction `PaymentGatewayInterface` + `StripePaymentGateway` (Adapter) + `PaymentService` (charge, crée Payment, lie Reservation, mail) · `PaymentFailedException` (exception domaine)
  - [x] classe `final`, promoted readonly, retours typés
- [x] **P3-1** Migrations Doctrine — 21 migrations 2020 cassées → 1 migration baseline (`Version20260621092107`, FK-safe via `SET FOREIGN_KEY_CHECKS`). `make migrate` OK.

## P1 — Architecture & SOLID

- [x] **P1-1** Extraction de la logique métier des contrôleurs
  - [x] `TravelRepository::findRandom(6)` (remplace `array_rand` inline)
  - [x] `ReservationPricingService::applyPrice()` (calcul prix hors `summary`)
  - [x] `ReservationService::validate()` (serial, stock, merge, persist+flush dans le service) + `NotEnoughStockException`
  - [x] `ContactService::handle(ContactDto)` (persist + mail)
  - [x] `InvoicePdfGenerator::generate()` (HTML Twig / Dompdf hors contrôleur) ; `invoicePdf` retourne une `Response`
  - [x] `UserService::setPassword()` (hash centralisé) — retire l'encoder du `SecurityController`
- [x] **P1-2** Standardiser les contrôleurs
  - [x] `final` partout (`Contact`, `Payment`, `Security`, `User`, `Reservation`, `Travel`, `Invoice`)
  - [x] promoted readonly constructors
  - [x] FQCN retirés (`Contact`, `Invoice`)
  - [x] `InvoicePdf` → `Response` (`StreamedResponse`/`Response`)
- [x] **P1-3** Interfaces de repositories (skill §3) — **tous les repositories injectés ont maintenant une interface**
  - [x] `ReservationRepositoryInterface` + `StayRepositoryInterface` (services)
  - [x] `TravelRepositoryInterface`, `OptionRepositoryInterface`, `CategoryRepositoryInterface`, `UserRepositoryInterface` (contrôleurs) — étendent `Doctrine\Persistence\ObjectRepository` (exposent find/findBy/findAll/findOneBy) + méthodes custom
  - [x] Swap DI : services + contrôleurs dépendent des interfaces ; alias services.yaml interface→impl ; mocks de tests basculés sur les interfaces
  - [x] bug latent du renommage corrigé : `StayRepositoryInterface` (classe restée plurielle dans fichier singulier) → singulier partout
- [~] **P1-4** Entités : Value Objects + invariants + renommage
  - [x] typage natif de toutes les propriétés (79 → 0 non typées)
  - [x] génériques Doctrine `Collection<int, X>` · getters non-nullable cohérents
  - [x] invariant `Reservation::markAsPaid()` (garde anti double-paiement) — `PaymentService` l'utilise au lieu de `setPayment()` externe · test unitaire `ReservationTest`
  - [ ] Value Object `Money` pour `Reservation.price` — **différé** (risque élevé : migration schéma float→int + conversion données + màj tous les templates Twig qui affichent `price`/`amount`)
  - [x] Value Object `Email` pour `User.email` / `Contact.email` — **fait** (skill §3)
    - VO `App\ValueObject\Email` (readonly, invariant format RFC garanti à la construction via `filter_var`, `__toString`, `equals` insensible à la casse)
    - Entités : propriété persistée `string $email` **inchangée** (colonne VARCHAR, `schema:validate` reste in sync, `#[UniqueEntity]`/`#[Assert\Email]` préservés) ; `getEmail(): Email` renvoie le VO ; `setEmail(Email)` accepte le VO et stocke la string. `UniqueEntityValidator` lit la **propriété** (pas le getter) → le rejet duplicate reste fonctionnel (vérifié Cypress register).
    - Call-sites : construction `new Email(...)` (UserController, ContactService, UserFixtures, tests) ; conversion `->value()` aux frontières string (EditUserDto::fromUser, MailerService, PaymentService, ContactService).
    - `Traveler.email` **fait (strict)** : `getEmail(): Email` + `setEmail(Email)` strict. `TravelerType` binde désormais le DTO `TravelerDto` (skill §8), pas l'entité — le formulaire n'écrit plus l'entité, donc `setEmail` peut être strict (plus d'union). Conversion DTO→entité via `TravelersDto::toTravelers()` (`new Email(...)`). Contraintes (NotBlank/Length/Email/LessThan) déplacées sur le DTO. Propriété persistée `string` inchangée (schéma + flux session préservés). Testé par `TravelerTest` + `ReservationFunctionalTest` (submit voyageurs → summary affiche l'email via le VO).
    - Testé : `EmailTest` (4 tests : format valide/invalid, empty, equals).
  - [ ] entités `final` — **non applicable** : Doctrine génère des proxies qui étendent l'entité (lazy loading) → `final` casserait le lazy loading des associations
  - [x] **renommage des entités au pluriel** : `Categories→Category`, `Options→Option`, `Pictures→Picture`, `Stays→Stay` ✅ — classes PHP + repositories (`CategoryRepository` etc.) + `StayRepositoryInterface` + CRUD controllers renommés. **Schéma DB inchangé** via `#[ORM\Table(name: 'plural')]` sur les 4 entités + `#[ORM\JoinTable/JoinColumn/InverseJoinColumn]` explicites (avec `onDelete: CASCADE`) sur les 2 ManyToMany de Reservation pour garder les tables/colonnes/FK `reservation_options`/`reservation_stays`. **Aucune migration, aucune conversion de données**. Validé par PHPStan (0) + `make qa` (25/73) + Cypress (11/11) + `doctrine:schema:validate` (in sync).
- [x] **P1-5** DTOs pour les formulaires
  - [x] `ContactType` sur `ContactDto` (DTO au lieu de l'entité)
  - [x] `RegisterType` sur `RegisterDto` + mapping DTO→User dans le contrôleur (hash via `UserService`, encoder retiré du `UserController`) — validé par Cypress register 6/6
  - [x] `EditUserType` sur `EditUserDto` (pré-rempli via `EditUserDto::fromUser()`, re-mappé sur l'entité au submit) — validé par `EditUserFunctionalTest` (2 tests : rendu + submit→dashboard)
  - [x] `TravelerType` sur `TravelerDto` (skill §8) — `TravelersType` binde `TravelersDto` (enveloppe la collection) ; conversion DTO→entité dans `TravelersDto::toTravelers()` ; contrôleur `configureTravelers` découplé de l'entité. Validé par `ReservationFunctionalTest` (5 tests).
  - [ ] API JSON : `#[MapRequestPayload]` + DTOs (si endpoints JSON prévus — aucun endpoint JSON actuellement)
  - [note] DTOs mutables à propriétés publiques (pas `readonly`) : Symfony Form ne peut pas écrire dans une propriété `readonly` sans factory `empty_data` verbeuse. La séparation du concept de l'entité est l'objectif (skill §8), l'immutabilité est secondaire.
- [x] **P1-6** Nettoyer les services
  - [x] `MakeSerialService` : `final` + `random_int()` (au lieu de `rand()`)
  - [x] `UserService` : `final` + `setPassword()` centralisé
  - [x] `StockManagementService` : param `Reservation` typé
  - [x] `SlugifyService` : supprimé (dead code, 0 référence) — les entités gardent `new Slugify()` dans les lifecycle callbacks (Doctrine ne permet pas l'injection dans les callbacks ; utilitaire sans état, acceptable)
  - [x] `ReservationMergeService` : boucles « reload managed entity » factorisées en 2 helpers typés `managedOptions()` / `managedStays()` (DRY)
- [x] **P1-7** `EasyAdminSubscriber` — supprimé (événement `easy_admin.pre_persist` jamais dispatché par EasyAdmin v5 ; `CollectionField` gère la persistance)

## P2 — Tests

- [~] **P2-1** Config PHPUnit + tests bootables
  - [x] `KERNEL_CLASS`, `SYMFONY_PHPUNIT_VERSION 10`
  - [x] env test bootable (`config/packages/test/framework.yaml` `storage_factory_id`, `security.yaml` `entry_point`)
  - [x] DB de test `dock_test` isolée + `make test-db` / `make test` / `make qa`
  - [x] `<listeners>` → `<extensions>` (SymfonyExtension) ; `<filter>` obsolète retiré
  - [x] 3 tests corrigés (isolation `tearDown` + `ensureKernelShutdown`) — `make qa` vert
  - [ ] éliminer les 4 « risky: did not remove its own exception handlers » (cosmétique, WebTestCase+PHPUnit 13)
  - [ ] `bootstrap` phpunit → `tests/bootstrap.php` (actuellement `config/bootstrap.php`)
- [x] **P2-2** Tests unitaires des services extraits
  - [x] `MakeSerialServiceTest`, `ReservationPricingServiceTest`, `UserServiceTest`, `PaymentServiceTest`, `StockManagementServiceTest`, `ExpiredReservationCleanupServiceTest`, `ContactServiceTest`, `EmailTest` (8 classes, 17 tests)
- [x] **P2-3** Tests fonctionnels étendus
  - [x] `LoginFunctionalTest`: login réussi (valid creds → redirect /) + login échoué (invalid → reste /login)
  - [x] `AccessControlFunctionalTest`: page réservée `/reservation/list/` et `/profil/dashboard` redirigent un anonyme vers /login
  - [x] `ContactFunctionalTest`: smoke test du rendu du formulaire contact (sur DTO)
  - [x] `ReservationFunctionalTest`: flux complet `index → configure options → configureTravelers → summary` (5 tests). Couvre le chemin critique multi-étapes (session) + valide le VO Email sur Traveler bout-en-bout. **Filet de sécurité pour la refonte TravelerDto (skill §8).**
  - [x] **Bug préexistant découvert et corrigé** : `summary` 500 (`Travel::$name` proxy Doctrine détaché après désérialisation session) → ajout de `ReservationController::refreshStays()` qui recharge les stays en entités managées (même pattern que `configure`). `StayRepositoryInterface` étend désormais `ObjectRepository` (expose `find()`).
  - [ ] soumission contact end-to-end (différée : contamination WebTestCase / CSRF rend le test fonctionnel de submit instable — la voie DTO→ContactService est couverte par le smoke + la logique est unit-testable)

## P3 — Outillage & configuration

- [x] **P3-2** PHPStan level 10 (447 → 0 ; strict + Symfony + Doctrine + PHPUnit activés)
- [x] **P3-5** `make qa` + `make test` + `make test-db`
- [x] **P3-3** Rector — config PHP 8.4 + sets attributs Symfony/Doctrine appliquée sur `src` (22 fichiers modernisés : `readonly class` sur les services sans état, first-class callables `[$this, 'm']` → `$this->m(...)`, `catch (X $e)` → `catch (X)` quand $e inutilisé). Validé par `make qa` + Cypress (rien cassé). `rector.phar` vide laissé (binaire `vendor/bin/rector` utilisé).
- [x] **P3-4** `services.yaml` — excl `Tests` (stale) retiré ; paramètres Stripe + alias `PaymentGatewayInterface` + alias repositories interfaces déclarés
  - [ ] déclarer `cocur/slugify` comme service (lié à P1-6)

## P4 — Nettoyage

- [~] **P4-1** Typos & conventions
  - [x] `sendConfirmedPaimenent → sendConfirmedPayment` (+ appelant)
  - [x] `findStockByid → findStockById`
  - [x] `seservationMergeService → reservationMergeService`
  - [x] route `user_resendactivatetoken → user_resend_activation_token` ; méthode `resendactivatetoken → resendActivationToken`
  - [x] `InvoiceHtml/InvoicePdf → invoiceHtml/invoicePdf` (camelCase)
  - [x] flash « voyoyage → voyage »
  - [x] `PictureCommand` chemin MAMP en dur → `$projectDir` injecté
  - [ ] commentaires EN/FR mélangés → uniformiser (FR)
- [~] **P4-2** Anti-patterns résiduels
  - [x] `EasyAdminSubscriber` supprimé (plus de `instanceof` dedans)
  - [x] `rand()` → `random_int()` (MakeSerialService)
  - [~] `instanceof` dans `UserRepository::upgradePassword` → **gardé** : c'est le pattern officiel Symfony pour `PasswordUpgraderInterface` (reçoit un `PasswordAuthenticatedUserInterface` quelconque, doit vérifier le type concret). Justifié, pas un anti-pattern.
  - [x] `new ArrayCollection()` dans `ReservationMergeService` : factorisé en helpers typés (DRY)

---

## Commits de la session (du + récent au + ancien)

1. `576d394` docs(task) — màj état global
2. `2ceebf7` P2-3 tests fonctionnels étendus (login, access control, contact)
3. `15ab3b1` P1-5 RegisterType sur RegisterDto (DTO pas entité, validé Cypress 6/6)
4. `121a307` P1-6/P4-2 cleanup services (SlugifyService mort, ReservationMerge DRY)
5. `421c565` P1-4a invariant Reservation::markAsPaid + test
6. `ddd6b48` *(hors refactoring — commit utilisateur : fixtures/config Cypress)*
7. `c3873d7` P2-1/Cypress register.cy.js vert (11/11) — fix flash/assertion
8. `2e70a2b` docs : ajout task.md
9. `39ce2b6` P1-3 interfaces de repository + DIP
10. `5960576` P2-2 tests unitaires des services (6 classes, 12 tests)
11. `2472bb7` P1-1 extraction de la logique métier des contrôleurs (6 services)
12. `810f518` quick wins (P0-1/P1-6/P1-7/P1-2/P4/P3-4)
13. `47d8cae` P2-1 make qa vert (tests isolés + config phpunit moderne)
14. `5763d8c` fix(test) env test bootable + DB de test dédiée dock_test
15. `8f8cdc6` docs plan (coche P0-2/P3-1)
16. `7c99037` P0-2 extraction Stripe (PaymentGateway + PaymentService)
17. `dd9161c` P3-1 migration baseline (21 migrations cassées → 1)
18. `8d37397` P3-2 PHPStan level 10 (447 → 0)

*(au-dessus de `8d37397` : pré-existant, voir `git log`)*

## Suite recommandée (restant = risqué / faible valeur)

Restant principalement:
1. ~~P1-4b renommage entités au pluriel~~ ✅ fait (schéma inchangé via #[ORM\Table] + JoinTable explicites)
2. ~~P1-4 VO Email~~ ✅ fait (User + Contact strict ; Traveler via setter union). Reste: refonte DTO §8 de TravelerType (nécessite un test fonctionnel du flux reservation d'abord).
3. **P1-4 VO Money** — **différé (bloqueur technique)** : `price` utilisé dans ~20 expressions Twig avec arithmétique (3 entités), dont un calcul de TVA dans la facture. Un VO `Money` casserait l'echo + l'arithmétique Twig → nécessite de déplacer les calculs des templates vers les services + extension Twig de rendu. Refonte conséquente, différée.
3. ~~P3-3 Rector~~ ✅ fait (22 fichiers modernisés, validé suite)
4. ~~P1-5 EditUserType DTO~~ ✅ fait (EditUserDto + EditUserFunctionalTest)
5. ~~P1-3 reste~~ ✅ fait (interfaces pour tous les repos injectés : Travel, Option, Category, User)
6. **P4-1 reste** — uniformisation commentaires EN/FR (cosmétique).

Décision : les 1 et 2 sont les plus conformes au skill mais les plus risqués ("ne rien casser"). À valider explicitement avant de lancer.

## Notes / risques connus

- `make qa` affiche **9 « risky »** + 5 « notices » (mocking Doctrine / handlers WebTestCase+PHPUnit 13) — bruit cosmétique, n'affecte pas le code de sortie (0). Les risky viennent des WebTestCase (kernel boot + exception handlers) ; les notices du mocking Doctrine.
- `bin/console --env=test` ne charge pas `.env.test` (`.env.local` surcharge) → les cibles Makefile passent `DATABASE_URL` en env réel (prioritaire) pour cibler `dock_test`.
- Ne JAMAIS lancer de commande mutative (`doctrine:schema:*`, `fixtures:load`, `app:unpaidorder`) sans `--env=test` + `DATABASE_URL` forcé — sinon risque d'écraser la DB dev `dock` (ça arrive 2x pendant la session, restaurée depuis backup).