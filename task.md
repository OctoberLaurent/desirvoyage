# Task — Conformité au skill `symfony-php-clean-code`

Suivi en temps réel de l'état des tâches. Source de vérité = le code (pas le plan détaillé `docs/refactoring-tasks.md` qui est plus verbeux).

Légende : `[x]` fait · `[~]` partiellement · `[ ]` à faire.

**État global : `make qa` vert** (PHPStan level 10 = 0 erreur, PHP-CS-Fixer = 0, PHPUnit 16 tests / 50 assertions, app HTTP 200, dev DB `dock` intacte).

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
- [~] **P1-2** Standardiser les contrôleurs
  - [x] `final` partout (`Contact`, `Payment`, `Security`, `User`, `Reservation`, `Travel`, `Invoice`)
  - [x] promoted readonly constructors
  - [x] FQCN retirés (`Contact`, `Invoice`)
  - [x] `InvoicePdf` → `Response` (`StreamedResponse`/`Response`)
  - [ ] (rien de restant — vérifier au final)
- [~] **P1-3** Interfaces de repositories (skill §3)
  - [x] `ReservationRepositoryInterface` + `StaysRepositoryInterface` (créées, implémentées, aliasées dans `services.yaml`)
  - [x] Swap DI : `ExpiredReservationCleanupService`, `StockManagementService` dépendent des interfaces ; mocks basculés sur les interfaces
  - [ ] étendre aux repositories injectés dans les contrôleurs (`Travel`, `Options`, `Categories`, `User`) — friction : nécessite de redéclarer `find/findBy/findAll` dans l'interface (à évaluer)
- [~] **P1-4** Entités : Value Objects + invariants + renommage
  - [x] typage natif de toutes les propriétés (79 → 0 non typées)
  - [x] génériques Doctrine `Collection<int, X>` · getters non-nullable cohérents
  - [x] invariant `Reservation::markAsPaid()` (garde anti double-paiement) — `PaymentService` l'utilise au lieu de `setPayment()` externe · test unitaire `ReservationTest`
  - [ ] Value Object `Money` pour `Reservation.price` — **différé** (risque élevé : migration schéma float→int + conversion données + màj tous les templates Twig qui affichent `price`/`amount`)
  - [ ] Value Object `Email` pour `User.email` / `Contact.email` — **différé** (touche UserInterface::getUserIdentifier, UniqueEntity, templates)
  - [ ] entités `final` — **non applicable** : Doctrine génère des proxies qui étendent l'entité (lazy loading) → `final` casserait le lazy loading des associations
  - [ ] **renommage des entités au pluriel** : `Categories→Category`, `Options→Option`, `Pictures→Picture`, `Stays→Stay` (gros impact : ORM croisé, repos, controllers, forms, templates, EasyAdmin, migration) — **commit isolé, risqué**
- [~] **P1-5** DTOs pour les formulaires
  - [x] `ContactType` sur `ContactDto` (DTO au lieu de l'entité)
  - [x] `RegisterType` sur `RegisterDto` + mapping DTO→User dans le contrôleur (hash via `UserService`, encoder retiré du `UserController`) — validé par Cypress register 6/6
  - [ ] `EditUserType` sur DTO (profil edit)
  - [ ] API JSON : `#[MapRequestPayload]` + DTOs (si endpoints JSON prévus)
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
- [~] **P2-2** Tests unitaires des services extraits
  - [x] `MakeSerialServiceTest`, `ReservationPricingServiceTest`, `UserServiceTest`, `PaymentServiceTest`, `StockManagementServiceTest`, `ExpiredReservationCleanupServiceTest` (6 classes, 12 tests)
  - [ ] (rien de restant côté services déjà extraits)
- [x] **P2-3** Tests fonctionnels étendus
  - [x] `LoginFunctionalTest`: login réussi (valid creds → redirect /) + login échoué (invalid → reste /login)
  - [x] `AccessControlFunctionalTest`: page réservée `/reservation/list/` et `/profil/dashboard` redirigent un anonyme vers /login
  - [x] `ContactFunctionalTest`: smoke test du rendu du formulaire contact (sur DTO)
  - [ ] soumission contact end-to-end (différée : contamination WebTestCase / CSRF rend le test fonctionnel de submit instable — la voie DTO→ContactService est couverte par le smoke + la logique est unit-testable)

## P3 — Outillage & configuration

- [x] **P3-2** PHPStan level 10 (447 → 0 ; strict + Symfony + Doctrine + PHPUnit activés)
- [x] **P3-5** `make qa` + `make test` + `make test-db`
- [ ] **P3-3** Rector — `rector.phar` vide (0 byte) ; configurer sets PHP 8.4 + Symfony, lancer `vendor/bin/rector process src` et reviewer
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

1. `39ce2b6` P1-3 interfaces de repository + DIP
2. `5960576` P2-2 tests unitaires des services
3. `2472bb7` P1-1 extraction de la logique métier des contrôleurs
4. `810f518` quick wins (P0-1/P1-6/P1-7/P1-2/P4/P3-4)
5. `47d8cae` P2-1 make qa vert (tests isolés + config phpunit)
6. `5763d8c` fix(test) env test bootable + DB de test dédiée
7. `8f8cdc6` docs plan (coche P0-2/P3-1)
8. `7c99037` P0-2 extraction Stripe (PaymentGateway + PaymentService)
9. `dd9161c` P3-1 migration baseline
10. `8d37397` P3-2 PHPStan level 10 (447→0)

## Suite recommandée

1. **P1-4a** Value Objects (`Money`, `Email`) + invariants `Reservation` (risque modéré)
2. **P1-4b** renommage entités au pluriel (commit isolé, risqué, backup DB)
3. P1-5 (DTOs Register/EditUser) + P1-6 reste (Slugify injection, ReservationMerge DRY)
4. P3-3 Rector + P2-3 tests fonctionnels étendus
5. P4-1 reste (commentaires) + P4-2 reste (instanceof UserRepository, ArrayCollection factoriser)

## Notes / risques connus

- `make qa` affiche 4 « risky » + 5 « notices » (mocking Doctrine / handlers WebTestCase+PHPUnit 13) — bruit cosmétique, n'affecte pas le code de sortie (0).
- `bin/console --env=test` ne charge pas `.env.test` (`.env.local` surcharge) → les cibles Makefile passent `DATABASE_URL` en env réel (prioritaire) pour cibler `dock_test`.
- Ne JAMAIS lancer de commande mutative (`doctrine:schema:*`, `fixtures:load`, `app:unpaidorder`) sans `--env=test` + `DATABASE_URL` forcé — sinon risque d'écraser la DB dev `dock` (ça arrive 2x pendant la session, restaurée depuis backup).