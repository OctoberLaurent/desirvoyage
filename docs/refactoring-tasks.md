# Plan de refactoring — conformité `symfony-php-clean-code`

Référence : `.agents/skills/symfony-php-clean-code/SKILL.md`
État courant : PHPStan level 5 ✅, PHP-CS-Fixer ✅, PHPUnit 13 installé, EasyAdmin v5, Symfony 7.4, PHP 8.4.

> Priorités : **P0** = bugs/sécurité/quick wins · **P1** = architecture/SOLID · **P2** = tests · **P3** = outillage/config · **P4** = nettoyage.

---

## P0 — Bugs, sécurité, quick wins

### Tâche P0-1 — Corriger `IncrementalStaysNotPurchasedCommand`
Fichier : `src/Command/IncrementalStaysNotPurchasedCommand.php`
- [ ] **Bug** : `$seats .= $nbTravelers;` (concaténation de string sur un int) → remplacer par `$seats += $nbTravelers;` (initialiser `$seats = 0;` déjà fait).
- [ ] **Code review** : retirer `dump($interval);` (skill §9 : pas de `dump()`/`dd()`).
- [ ] **Bug potentiel** : `$date = $reservation->getCreatedDate();` peut être `null` → `getTimestamp()` fatal. Ajouter garde `if (null === $date) continue;`.
- [ ] **Console** : remplacer `protected static $defaultName = 'app:unpaidorder';` par l'attribut `#[AsCommand(name: 'app:unpaidorder', description: '...')]` (résout le warning « cannot have an empty name » au démarrage des containers).
- [ ] **SOLID** : constructeur non promu, props non `readonly`. Passer en promoted readonly ctor. Rendre la classe `final`.
- [ ] **Extraction** : sortir la logique métier (détection des réservations expirées + remontée de stock) dans un service `ExpiredReservationCleanupService` ; la commande ne fait que l'orchestration console.

### Tâche P0-2 — Sécuriser & refactorer `PaymentController`
Fichier : `src/Controller/PaymentController.php`
- [ ] **SÉCURITÉ critique** : la clé privée Stripe est passée au template Twig (`'privateKey' => $this->privateKey`) → retirer immédiatement du render. Ne jamais exposer la clé secrète côté client.
- [ ] **Anti-pattern** : `$_ENV['STRIPE_PUBLIC_KEY']` / `$_ENV['STRIPE_PRIVATE_KEY']` lus en dur → injecter via `ParameterBag` (paramètres `stripe.public_key` / `stripe.secret_key` dans `config/services.yaml` + `config/packages/`).
- [ ] **Refactor** : extraire la logique de charge Stripe dans un service `PaymentGatewayInterface` + `StripePaymentGateway` (Adapter, skill §2 Adapter). Le contrôleur ne fait que l'orchestration HTTP.
- [ ] Créer un `PaymentService` qui orchestre : créer `Payment`, lier à `Reservation`, envoyer mail via `MailerService`. Transaction dans le service, pas dans le contrôleur.
- [ ] Rendre la classe `final`, promoted readonly ctor.
- [ ] `catch (\Exception $e)` trop large → attraper `ApiErrorException` (Stripe) ou une exception domaine `PaymentFailedException`.
- [ ] Typage : `index(Reservation $reservation)` retourne `\Symfony\Component\HttpFoundation\Response` via FQCN → importer `Response`.

### Tâche P3-1 (déplacée ici car bloquante) — Régénérer les migrations Doctrine
- [ ] Les 21 migrations 2020 cassent (`MariaDB1052Platform::getName()` supprimé). Supprimer `src/Migrations/Version*.php` et générer une migration baseline unique depuis le schéma courant : `bin/console doctrine:migrations:diff` puis renommer en `Version_init`. Permet à `make migrate` de fonctionner sur un env neuf.

---

## P1 — Architecture & SOLID

### Tâche P1-1 — Extraire la logique métier des contrôleurs
Skill §1 SRP, §3 Controller rules, §9 checklist (« No business logic in controller »).

- [ ] **`ReservationController::summary`** (`src/Controller/ReservationController.php`) : calcul de prix (`$totalPrice = ($stayPrice + $optionsPrice) * $nbtravelers`) est de la logique métier → `ReservationPricingService::calculate(Reservation): Money`. Le contrôleur ne fait que render.
- [ ] **`ReservationController::validate`** : génération serial, décrément stock, merge, persist/flush → `ReservationService::validate(Reservation): Reservation`. La transaction (`flush`) doit être dans le service, pas le contrôleur.
- [ ] **`TravelController::index`** : tirage aléatoire (`array_rand`) → méthode `TravelRepository::findRandom(int $limit)`.
- [ ] **`ContactController::contact`** : `new Contact()`, `setSendDate(new \DateTime())`, persist/flush, envoi mail → `ContactService::handle(ContactDto)` (utiliser un DTO plutôt que l'entité dans le form).
- [ ] **`SecurityController`** (login/activate/reset/newpassword) : logique token/password → `UserService` (existe déjà, à étendre). Contrôleur = HTTP seulement.
- [ ] **`InvoiceController::InvoicePdf`** : génère du PDF dans le contrôleur → `InvoicePdfGenerator::generate(Reservation): string` (ou `StreamedResponse`). Séparer la génération HTML (Twig `renderView`) du rendu PDF (Dompdf).

### Tâche P1-2 — Standardiser les contrôleurs
- [ ] Rendre `final` : `ContactController`, `PaymentController`, `SecurityController`, `UserController` (déjà `final` pour `ReservationController`, `TravelController`, `InvoiceController`).
- [ ] Promoted readonly constructor partout (cf. `SecurityController` qui déclare 4 props privées non readonly + ctor manuel).
- [ ] Remplacer les FQCN en retour (`\Symfony\Component\HttpFoundation\Response`, `\Doctrine\ORM\EntityManagerInterface`) par `use` + type court.
- [ ] `InvoiceController::InvoicePdf` retourne `void` → doit retourner une `Response` (`StreamedResponse`/`BinaryFileResponse`), bonne pratique Symfony.

### Tâche P1-3 — Repository interfaces (skill §3 Repository rules)
Pour chacun des 11 repositories (`Categories`, `Contact`, `Formality`, `Options`, `Payment`, `Pictures`, `Reservation`, `Stays`, `Traveler`, `Travel`, `User`) :
- [ ] Créer une interface `*RepositoryInterface` (ex. `ReservationRepositoryInterface`) avec les méthodes utilisées.
- [ ] Faire implémenter l'interface par le repository concret.
- [ ] Injecter l'interface (pas la classe) dans services/contrôleurs ; aliaser interface → impl dans `services.yaml` (ou autowiring via `%App\Repository\XRepositoryInterface%: '@App\Repository\XRepository'`).
- [ ] Méthodes expressives : remplacer `findBy(['payment' => null])` par `findUnpaid()` dans `ReservationRepository`.

### Tâche P1-4 — Entités : typage, invariants, Value Objects (skill §3 Entity/VO rules)
- [ ] **Typage natif** : 79 propriétés d'entités sont non typées (`private $id;` etc.) → ajouter types PHP (`private ?int $id = null;`, etc.). Rector peut aider (`rector.php` existe).
- [ ] **Renommer les entités au pluriel** (anti-naming skill §6, conflit avec la table SQL) : `Categories` → `Category`, `Options` → `Option`, `Pictures` → `Picture`, `Stays` → `Stay`. Mettre à jour mappings ORM (`repositoryClass`, `targetEntity`), repositories, controllers, forms, templates, EasyAdmin CRUD. *(gros impact — planifier un commit dédié)*
- [ ] **Value Object `Money`** pour `Reservation.price` (float → Money{amount:int, currency:string}). Skill §3 VO + §8 Doctrine (Embedded).
- [ ] **Value Object `Email`** pour `User.email` / `Contact.email`.
- [ ] **Invariants/transition d'état** : `Reservation` devrait exposer `markAsPaid()` / `markAsCancelled()` avec garde d'état (skill §3 Entity rules + §2 State), plutôt que d'être manipulée de l'extérieur via `setPayment()`.
- [ ] Rendre les entités `final` où possible (sauf héritage Doctrine), constructeurs avec params requis.

### Tâche P1-5 — DTOs pour les formulaires (skill §8 Forms)
- [ ] `ContactController` bind `Contact` (entité) au form → créer `ContactDto` (readonly class) + `ContactType` sur le DTO, puis mapper vers `Contact` dans le service.
- [ ] Même approche pour `RegisterType` (création user), `MyPasswordType`/`RenewPasswordType` (déjà des "DTO" `MyPassword` non mappé — ok), `EditUserType`.
- [ ] API : si endpoints JSON prévus, utiliser `#[MapRequestPayload]` + DTOs.

### Tâche P1-6 — Nettoyer les services
- [ ] `MakeSerialService` : rendre `final`, utiliser `random_int()` (sécurisé) au lieu de `rand()`, ctor promoted readonly.
- [ ] `StockManagementService::decrementStock($reservation)` : typer le param `Reservation $reservation`, retourner un résultat clair, sortir la persistance du service (mutation stay stock = ok, mais le flush doit être au caller/service applicatif).
- [ ] `UserService` : rendre `final`, promoted readonly ctor.
- [ ] `SlugifyService` : `new Slugify()` dans la méthode (anti-pattern « new in service ») → injecter `SlugifyInterface` via DI (enregistrer `cocur/slugify` comme service).
- [ ] `ReservationMergeService` : factoriser la boucle « reload managed entity » dans une méthode privée générique (DRY) ; typer strictement.

### Tâche P1-7 — Revoir `EasyAdminSubscriber`
Fichier : `src/Event/EasyAdminSubscriber.php`
- [ ] Vérifier que l'événement `easy_admin.pre_persist` existe toujours avec EasyAdmin v5 (probablement obsolète — EasyAdmin 3+ utilise `BeforeEntityPersistEvent`). Si inutile, **supprimer** le subscriber.
- [ ] Si conservé : renommer `onPreUpdate` (lié à `pre_persist` → incohérent), rendre `final`, typer `getSubscribedEvents(): array`, éviter les `instanceof` (skill §7), utiliser un subscriber dédié par entité si besoin.

---

## P2 — Tests (skill §4)

### Tâche P2-1 — Corriger la config PHPUnit
Fichier : `phpunit.xml.dist`
- [ ] `SYMFONY_PHPUNIT_VERSION` vaut `7.5` mais PHPUnit 13 est installé → mettre à `10` (ou supprimer, utiliser le binaire installé via `vendor/bin/phpunit`).
- [ ] Vérifier `xsi:noNamespaceSchemaLocation` (pointe vers un path `.phpunit` inexistant).
- [ ] Ajouter `bootstrap="tests/bootstrap.php"` cohérent (actuellement `config/bootstrap.php`).

### Tâche P2-2 — Tests unitaires pour la logique métier extraite
Une fois P1-1 fait, couvrir :
- [ ] `ReservationPricingServiceTest` (KernelTestCase ou TestCase avec mocks) — prix attendu pour N voyageurs + options.
- [ ] `ExpiredReservationCleanupServiceTest` — réservation > 15 min → stock remonté + résa supprimée ; résa récente → inchangée.
- [ ] `MakeSerialServiceTest` — format `XXX-XXX-XXX`, longueur, unicité sur N tirages.
- [ ] `StockManagementServiceTest` — décrément cohérent avec `count(travelers)`.
- [ ] `UserServiceTest` — token généré + expiration à +1j ; `resetToken` annule.
- [ ] `PaymentServiceTest` (avec `StripePaymentGateway` mocké via `PaymentGatewayInterface`).

### Tâche P2-3 — Compléter les tests fonctionnels existants
- [ ] Les 3 tests (`MainPageControllerTest`, `LoginPageControllerTest`, `PageControllerTest`) sont des smoke tests `GET / → 200`. Ajouter : test login réussi/échoué, accès page réservée (`/reservation`) redirige si non auth, soumission formulaire contact.

---

## P3 — Outillage & configuration

### Tâche P3-2 — Monter PHPStan au niveau ≥ 6 (skill §8/§9) ✅
Fichier : `phpstan.dist.neon`
- [x] Passé `level: 5` → **`level: 10`**. Règles strict + Symfony + Doctrine + PHPUnit activées (via `phpstan/extension-installer`).
- [x] `symfony.containerXmlPath` configuré.
- [x] **0 erreur** après correction (de 447 erreurs initiales à 0).
- Corrections appliquées : typage natif de toutes les propriétés d'entités, génériques Doctrine `Collection<int, X>`, types de retour, `@extends` sur repositories/forms/CRUD, promoted readonly constructors, `final`, `#[AsCommand]`, retraits de `dump()`/casts inutiles, etc.

### Tâche P3-3 — Rector
- [ ] `rector.php` existe, `rector.phar` est vide (0 byte). Configurer un set `LevelSetList::UP_TO_PHP_84` + Symfony sets pour automatiser P1-4 (typage entités), P1-2 (promoted ctor), P0-1 (AsCommand). Lancer `vendor/bin/rector process src` et reviewer.

### Tâche P3-4 — services.yaml
- [ ] L'exclusion `Entity,Migrations,Tests` est stale (`Tests` n'est pas sous `src/`). Nettoyer : `exclude: '../src/{DependencyInjection,Entity,Migrations,Kernel.php}'`.
- [ ] Déclarer les paramètres Stripe (`stripe.public_key`, `stripe.secret_key`) et l'alias `cocur/slugify` ici.

### Tâche P3-5 — CI / Makefile
- [ ] Ajouter cible `make qa` = `cs-fix --dry-run && phpstan && phpunit`. Permet de garder la conformité avant commit.
- [ ] Ajouter `make test` = `docker exec php-fpm vendor/bin/phpunit`.

---

## P4 — Nettoyage de code (skill §6 naming, §7 anti-patterns)

### Tâche P4-1 — Typos & conventions de nommage
- [ ] `MailerService::sendConfirmedPaimenent` → `sendConfirmedPayment`.
- [ ] `StockManagementService::findStockByid` (dans `StaysRepository`) → `findStockById`.
- [ ] Variable `seservationMergeService` → `reservationMergeService` (dans `ReservationController`).
- [ ] Route `user_resendactivatetoken` → `user_resend_activation_token`.
- [ ] Méthode `resendactivatetoken` → `resendActivationToken`. `InvoiceHtml`/`InvoicePdf` → `invoiceHtml`/`invoicePdf` (camelCase méthodes).
- [ ] Flash message « OUPS ! Il n\'y a personne d\'enregistré sur ce voyoyage » → « voyage ».
- [ ] Commentaires anglais/français mélangés → uniformiser (FR conseillé pour ce projet).
- [ ] `PictureCommand::pictureName` contient un chemin en dur `/Applications/MAMP/htdocs/...` → paramétrer via `kernel.project_dir`.

### Tâche P4-2 — Anti-patterns résiduels (skill §7)
- [ ] `instanceof` dans `EasyAdminSubscriber` et `UserRepository` → préférer polymorphisme / double dispatch.
- [ ] Booléens implicites / paramètres booléens : auditer (ex. signatures à flag).
- [ ] `new ArrayCollection()` dans `ReservationMergeService` : ok (Value Object/collection), mais factoriser.
- [ ] `rand()` dans `MakeSerialService` → `random_int()` (déjà en P1-6).

---

## Ordre d'exécution recommandé

1. **Sprint 1 (P0)** — sécurité + bugs : P0-2 (Stripe), P0-1 (command), P3-1 (migrations). ~1 jour.
2. **Sprint 2 (P1 architectures stables)** — P1-1 (extraire services), P1-2 (final/readonly contrôleurs), P1-6 (services), P1-7 (EA subscriber). ~2-3 jours.
3. **Sprint 3 (P1 structure)** — P1-3 (repo interfaces), P1-4 (entités typage + VO + renommage pluriel), P1-5 (DTOs). Le renommage d'entités = commit dédié. ~3-4 jours.
4. **Sprint 4 (P2 tests)** — P2-1, P2-2, P2-3. ~2 jours.
5. **Sprint 5 (P3/P4 finition)** — P3-2/3/4/5, P4-1/2. ~1 jour.

Chaque tâche = 1 commit + `make qa` vert avant de pousser.