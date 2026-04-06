---
name: mate-ai
description: Brief description of what this skill does
---

## 1. Introduction

**Symfony AI Mate** est un composant qui fournit un **serveur MCP** (Model Context Protocol) pour votre application Symfony. Il permet à des assistants IA (comme Claude, Cline, etc.) d'interagir directement avec votre codebase via des **Tools** (outils/skills) personnalisés.

Concrètement, une **Skill Mate** est une classe PHP dont certaines méthodes sont exposées comme des outils MCP grâce à l'attribut `#[McpTool]`. L'assistant IA peut alors appeler ces méthodes pour :

- Rechercher des données dans votre base
- Générer des rapports
- Effectuer des actions métier
- Valider des informations

### Architecture

```
┌──────────────┐      MCP Protocol      ┌──────────────────┐
│  Client IA   │ ◄──────────────────► │  Mate Server      │
│  (Claude,    │    (stdio / HTTP)     │  (vendor/bin/mate │
│   Cline…)    │                       │   serve)          │
└──────────────┘                       └────────┬─────────┘
                                                │
                                    ┌───────────▼───────────┐
                                    │  mate/src/             │
                                    │  ├── TravelSearchTool  │
                                    │  ├── ReservationTool   │
                                    │  └── RecommendationTool│
                                    └───────────┬───────────┘
                                                │
                                    ┌───────────▼───────────┐
                                    │  App\Repository\*      │
                                    │  App\Entity\*          │
                                    │  (Doctrine + MySQL)    │
                                    └───────────────────────┘
```

---

## 2. Installation de Mate

> ⚠️ **Règle Docker** : Toutes les commandes PHP/Composer doivent être exécutées dans le conteneur `php` (service `php` dans `docker-symfony/docker-compose.yml`).

### Étape 1 — Installer le paquet

```bash
docker compose exec php composer require --dev symfony/ai-mate
```

### Étape 2 — Initialiser la configuration

```bash
docker compose exec php vendor/bin/mate init
```

Cette commande crée :
- `mate/` — Répertoire racine de configuration
- `mate/src/` — Répertoire pour vos Skills personnalisées
- `mate/config.php` — Fichier de configuration des services
- `mcp.json` — Fichier de configuration pour les clients MCP

Elle met aussi à jour votre `composer.json` avec :

```json
{
    "autoload": {
        "psr-4": {
            "App\\Mate\\": "mate/src"
        }
    },
    "extra": {
        "ai-mate": {
            "scan-dirs": ["mate/src"],
            "includes": ["mate/config.php"]
        }
    }
}
```

### Étape 3 — Mettre à jour l'autoloader

```bash
docker compose exec php composer dump-autoload
```

### Étape 4 — Découvrir les extensions disponibles

```bash
docker compose exec php vendor/bin/mate discover
```

Cette commande génère/met à jour `mate/extensions.php` qui liste toutes les extensions découvertes.

### Étape 5 — Lancer le serveur MCP

```bash
docker compose exec php vendor/bin/mate serve
```

---

## 3. Structure générée par `mate init`

```
desirvoyage/
├── mate/
│   ├── src/                    # Vos Skills personnalisées (namespace: App\Mate\)
│   ├── config.php              # Configuration des services (injection de dépendances)
│   └── extensions.php          # Extensions découvertes (auto-généré)
├── mcp.json                    # Configuration client MCP
├── composer.json               # Mis à jour avec autoload + extra.ai-mate
└── src/
    ├── Entity/
    │   ├── Travel.php          # Entités existantes du projet
    │   ├── Reservation.php
    │   ├── Stays.php
    │   └── ...
    └── Repository/
        ├── TravelRepository.php
        ├── ReservationRepository.php
        └── ...
```

---

## 4. Concepts fondamentaux

### 4.1 L'attribut `#[McpTool]`

C'est le cœur d'une Skill Mate. Cet attribut PHP transforme une méthode classique en outil MCP accessible par l'IA.

```php
use Mcp\Capability\Attribute\McpTool;

class MonTool
{
    #[McpTool(name: 'mon-outil', description: 'Description claire de ce que fait cet outil')]
    public function execute(string $parametre): string
    {
        return 'Résultat : ' . $parametre;
    }
}
```

**Paramètres de l'attribut :**

| Paramètre     | Type   | Requis | Description                                          |
|---------------|--------|--------|------------------------------------------------------|
| `name`        | string | ✅     | Nom unique de l'outil (kebab-case recommandé)        |
| `description` | string | ✅     | Description claire pour l'IA — doit expliquer le but |

### 4.2 Injection de dépendances

Les dépendances du constructeur sont **automatiquement injectées** par le conteneur Mate :

```php
use Psr\Log\LoggerInterface;

class MonTool
{
    public function __construct(
        private LoggerInterface $logger,
    ) {}

    #[McpTool(name: 'mon-outil', description: '...')]
    public function execute(string $param): string
    {
        $this->logger->info('Tool exécuté', ['param' => $param]);
        return 'Résultat : ' . $param;
    }
}
```

### 4.3 Types de retour

Les méthodes tool peuvent retourner :
- `string` — Texte simple
- `array` — Données structurées (JSON)
- `object` — Objet sérialisable

### 4.4 Paramètres de méthode

Les paramètres de la méthode sont automatiquement exposés comme paramètres de l'outil MCP. L'IA doit fournir ces valeurs lors de l'appel.

```php
#[McpTool(name: 'search-travels', description: 'Recherche des voyages')]
public function search(string $destination, float $maxPrice): array
{
    // $destination et $maxPrice sont fournis par l'IA
}
```

---

## 5. Créer une Skill (Tool) personnalisée

### Workflow de création

```
1. Créer la classe PHP dans mate/src/
2. Ajouter l'attribut #[McpTool] sur la méthode
3. Enregistrer le service dans mate/config.php (si nécessaire)
4. Tester avec vendor/bin/mate mcp:tools:call
```

### Template de base

```php
<?php

// mate/src/MaSkill.php

namespace App\Mate;

use Mcp\Capability\Attribute\McpTool;

class MaSkill
{
    #[McpTool(
        name: 'ma-skill',
        description: 'Description précise de ce que fait cette skill. '
                   . 'Inclure le contexte et les cas d\'utilisation.'
    )]
    public function execute(string $parametre): array
    {
        try {
            // Logique métier ici
            return [
                'success' => true,
                'data' => 'résultat',
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
```

---

## 6. Exemples de Skills pour DésirVoyage

### 6.1 TravelSearchTool — Rechercher des voyages

Cette skill permet à l'IA de rechercher des voyages dans le catalogue DésirVoyage par catégorie, destination et budget.

```php
<?php

// mate/src/TravelSearchTool.php

namespace App\Mate;

use App\Repository\TravelRepository;
use App\Repository\CategoriesRepository;
use Mcp\Capability\Attribute\McpTool;

class TravelSearchTool
{
    public function __construct(
        private TravelRepository $travelRepository,
        private CategoriesRepository $categoriesRepository,
    ) {}

    #[McpTool(
        name: 'search-travels',
        description: 'Recherche des voyages dans le catalogue DésirVoyage. '
                   . 'Permet de filtrer par catégorie, destination (ville de départ) et prix maximum. '
                   . 'Retourne une liste de voyages avec leurs détails.'
    )]
    public function search(
        string $category = '',
        string $departure = '',
        float $maxPrice = 0,
    ): array {
        $qb = $this->travelRepository->createQueryBuilder('t')
            ->leftJoin('t.categories', 'c')
            ->leftJoin('t.stays', 's')
            ->addSelect('c', 's');

        if ('' !== $category) {
            $qb->andWhere('c.title LIKE :category')
               ->setParameter('category', '%' . $category . '%');
        }

        if ('' !== $departure) {
            $qb->andWhere('s.depature LIKE :departure')
               ->setParameter('departure', '%' . $departure . '%');
        }

        if ($maxPrice > 0) {
            $qb->andWhere('s.price <= :maxPrice')
               ->setParameter('maxPrice', $maxPrice);
        }

        $travels = $qb->getQuery()->getResult();

        return array_map(fn ($travel) => [
            'name' => $travel->getName(),
            'subtitle' => $travel->getSubtitle(),
            'description' => $travel->getDescriptions(),
            'category' => $travel->getCategories()?->getTitle(),
            'minPrice' => $travel->getMinPrice(),
        ], $travels);
    }

    #[McpTool(
        name: 'list-categories',
        description: 'Liste toutes les catégories de voyages disponibles dans DésirVoyage.'
    )]
    public function listCategories(): array
    {
        $categories = $this->categoriesRepository->findAll();

        return array_map(fn ($category) => [
            'title' => $category->getTitle(),
            'slug' => $category->getSlug(),
        ], $categories);
    }
}
```

### 6.2 ReservationSummaryTool — Résumer une réservation

Cette skill permet à l'IA de récupérer le résumé d'une réservation à partir de son numéro de série.

```php
<?php

// mate/src/ReservationSummaryTool.php

namespace App\Mate;

use App\Repository\ReservationRepository;
use Mcp\Capability\Attribute\McpTool;

class ReservationSummaryTool
{
    public function __construct(
        private ReservationRepository $reservationRepository,
    ) {}

    #[McpTool(
        name: 'reservation-summary',
        description: 'Récupère le résumé détaillé d\'une réservation à partir de son numéro de série. '
                   . 'Inclut les informations voyage, voyageurs, options et paiement.'
    )]
    public function getSummary(string $serial): array
    {
        $reservation = $this->reservationRepository->findOneBy(['serial' => $serial]);

        if (null === $reservation) {
            return [
                'success' => false,
                'error' => sprintf('Aucune réservation trouvée avec le numéro "%s".', $serial),
            ];
        }

        // Informations sur les séjours (voyages réservés)
        $stays = [];
        foreach ($reservation->getStays() as $stay) {
            $stays[] = [
                'travel' => $stay->getTravel()?->getName(),
                'departure' => $stay->getDepature(),
                'arrival' => $stay->getArrival(),
                'startDate' => $stay->getStarDate()?->format('d/m/Y'),
                'endDate' => $stay->getEndDate()?->format('d/m/Y'),
                'price' => $stay->getPrice(),
            ];
        }

        // Informations sur les voyageurs
        $travelers = [];
        foreach ($reservation->getTravelers() as $traveler) {
            $travelers[] = [
                'firstName' => $traveler->getFirstName(),
                'lastName' => $traveler->getLastName(),
            ];
        }

        // Informations sur les options
        $options = [];
        foreach ($reservation->getOptions() as $option) {
            $options[] = [
                'name' => $option->getName(),
                'price' => $option->getPrice(),
            ];
        }

        return [
            'success' => true,
            'serial' => $reservation->getSerial(),
            'totalPrice' => $reservation->getPrice(),
            'createdAt' => $reservation->getCreatedDate()?->format('d/m/Y H:i'),
            'stays' => $stays,
            'travelers' => $travelers,
            'options' => $options,
            'paymentStatus' => $reservation->getPayment()?->getStatus() ?? 'non défini',
        ];
    }

    #[McpTool(
        name: 'user-reservations',
        description: 'Liste toutes les réservations d\'un utilisateur donné (par son email).'
    )]
    public function getUserReservations(string $email): array
    {
        $reservations = $this->reservationRepository->createQueryBuilder('r')
            ->join('r.user', 'u')
            ->where('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getResult();

        if (empty($reservations)) {
            return [
                'success' => false,
                'error' => sprintf('Aucune réservation trouvée pour "%s".', $email),
            ];
        }

        return [
            'success' => true,
            'count' => count($reservations),
            'reservations' => array_map(fn ($r) => [
                'serial' => $r->getSerial(),
                'price' => $r->getPrice(),
                'createdAt' => $r->getCreatedDate()?->format('d/m/Y'),
            ], $reservations),
        ];
    }
}
```

### 6.3 TravelRecommendationTool — Recommander des voyages

Cette skill permet à l'IA de recommander des voyages en fonction de critères comme le budget, la durée souhaitée et la période de l'année.

```php
<?php

// mate/src/TravelRecommendationTool.php

namespace App\Mate;

use App\Repository\TravelRepository;
use Mcp\Capability\Attribute\McpTool;

class TravelRecommendationTool
{
    public function __construct(
        private TravelRepository $travelRepository,
    ) {}

    #[McpTool(
        name: 'recommend-travels',
        description: 'Recommande des voyages DésirVoyage basés sur un budget, une durée en jours '
                   . 'et un mois de départ souhaité. Retourne les voyages triés par pertinence.'
    )]
    public function recommend(
        float $budget = 0,
        int $durationDays = 0,
        int $departureMonth = 0,
    ): array {
        $qb = $this->travelRepository->createQueryBuilder('t')
            ->leftJoin('t.stays', 's')
            ->leftJoin('t.categories', 'c')
            ->addSelect('s', 'c');

        // Filtre par budget
        if ($budget > 0) {
            $qb->andWhere('s.price <= :budget')
               ->setParameter('budget', $budget);
        }

        // Filtre par mois de départ
        if ($departureMonth >= 1 && $departureMonth <= 12) {
            $qb->andWhere('MONTH(s.starDate) = :month')
               ->setParameter('month', $departureMonth);
        }

        $travels = $qb->getQuery()->getResult();

        $results = [];
        foreach ($travels as $travel) {
            $stays = $travel->getStays()->toArray();

            // Filtre par durée si spécifiée
            if ($durationDays > 0) {
                $stays = array_filter($stays, function ($stay) use ($durationDays) {
                    $diff = $stay->getStarDate()?->diff($stay->getEndDate());
                    return null !== $diff && $diff->days <= $durationDays;
                });
            }

            if (empty($stays)) {
                continue;
            }

            $stayData = array_map(fn ($stay) => [
                'departure' => $stay->getDepature(),
                'arrival' => $stay->getArrival(),
                'startDate' => $stay->getStarDate()?->format('d/m/Y'),
                'endDate' => $stay->getEndDate()?->format('d/m/Y'),
                'price' => $stay->getPrice(),
                'stock' => $stay->getStock(),
            ], $stays);

            $results[] = [
                'name' => $travel->getName(),
                'subtitle' => $travel->getSubtitle(),
                'description' => $travel->getDescriptions(),
                'category' => $travel->getCategories()?->getTitle(),
                'minPrice' => $travel->getMinPrice(),
                'availableStays' => $stayData,
            ];
        }

        return [
            'success' => true,
            'count' => count($results),
            'recommendations' => $results,
        ];
    }
}
```

---

## 7. Enregistrement dans `mate/config.php`

Le fichier `mate/config.php` permet de configurer l'injection de dépendances pour vos Skills. Mate utilise un conteneur de services simple.

### Exemple complet

```php
<?php

// mate/config.php

use App\Mate\TravelSearchTool;
use App\Mate\ReservationSummaryTool;
use App\Mate\TravelRecommendationTool;
use App\Repository\TravelRepository;
use App\Repository\ReservationRepository;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Configuration des services pour Mate.
 *
 * Les repositories Doctrine sont récupérés via l'EntityManager.
 * Mate injectera automatiquement ces dépendances dans vos Skills.
 */

// Récupérer l'EntityManager Doctrine
$entityManager = $container->get(EntityManagerInterface::class);

// Enregistrer les repositories
$container->set(TravelRepository::class, $entityManager->getRepository(\App\Entity\Travel::class));
$container->set(CategoriesRepository::class, $entityManager->getRepository(\App\Entity\Categories::class));
$container->set(ReservationRepository::class, $entityManager->getRepository(\App\Entity\Reservation::class));

// Enregistrer les Skills
$container->set(TravelSearchTool::class);
$container->set(ReservationSummaryTool::class);
$container->set(TravelRecommendationTool::class);
```

> **Note** : La syntaxe exacte de `mate/config.php` peut varier selon la version de Mate. Consultez la documentation officielle pour les détails spécifiques à votre version : https://symfony.com/doc/current/ai/components/mate.html

---

## 8. Configuration MCP pour les clients IA

Le fichier `mcp.json` à la racine du projet configure la connexion entre le client IA et le serveur Mate.

### Exemple pour un client IA type (Cline, Claude Desktop, etc.)

```json
{
    "mcpServers": {
        "desirvoyage": {
            "command": "docker",
            "args": [
                "compose",
                "exec",
                "php",
                "vendor/bin/mate",
                "serve"
            ],
            "cwd": "/var/www/symfony"
        }
    }
}
```

### Alternative avec `php` directement (environnement local)

```json
{
    "mcpServers": {
        "desirvoyage": {
            "command": "php",
            "args": [
                "vendor/bin/mate",
                "serve"
            ]
        }
    }
}
```

---

## 9. Tester et déboguer les Skills

### Lister tous les outils disponibles

```bash
docker compose exec php vendor/bin/mate mcp:tools:list
```

### Inspecter un outil spécifique

```bash
docker compose exec php vendor/bin/mate mcp:tools:inspect search-travels
```

### Exécuter un outil avec des paramètres

```bash
# Sans paramètres
docker compose exec php vendor/bin/mate mcp:tools:call list-categories '{}'

# Avec paramètres
docker compose exec php vendor/bin/mate mcp:tools:call search-travels '{
    "category": "plage",
    "maxPrice": 1500
}'

# Résumé d'une réservation
docker compose exec php vendor/bin/mate mcp:tools:call reservation-summary '{
    "serial": "ABC123"
}'

# Recommandations
docker compose exec php vendor/bin/mate mcp:tools:call recommend-travels '{
    "budget": 2000,
    "departureMonth": 7
}'

# Format JSON
docker compose exec php vendor/bin/mate mcp:tools:call search-travels '{}' --format=json
```

### Déboguer les capacités

```bash
# Lister toutes les capacités
docker compose exec php vendor/bin/mate debug:capabilities

# Lister uniquement les tools
docker compose exec php vendor/bin/mate debug:capabilities --type=tool
```

---

## 10. Bonnes pratiques

### ✅ Nommage

- **Nom d'outil** : Utilisez le `kebab-case` (ex : `search-travels`, `reservation-summary`)
- **Nom de classe** : Utilisez le `PascalCase` avec le suffixe `Tool` (ex : `TravelSearchTool`)
- **Description** : Soyez précis et exhaustif — l'IA se base sur cette description pour décider quand utiliser l'outil

### ✅ Descriptions efficaces

Une bonne description explique :
1. **Ce que fait l'outil** (action)
2. **Quand l'utiliser** (contexte)
3. **Ce qu'il retourne** (résultat attendu)

```php
// ❌ Mauvais
#[McpTool(name: 'search', description: 'Search')]

// ✅ Bon
#[McpTool(
    name: 'search-travels',
    description: 'Recherche des voyages dans le catalogue DésirVoyage. '
               . 'Permet de filtrer par catégorie, destination et prix maximum. '
               . 'Retourne une liste de voyages avec nom, description et prix minimal.'
)]
```

### ✅ Gestion des erreurs

Toujours gérer les cas d'erreur de manière élégante :

```php
#[McpTool(name: 'my-tool', description: '...')]
public function execute(string $serial): array
{
    $entity = $this->repository->findOneBy(['serial' => $serial]);

    if (null === $entity) {
        return [
            'success' => false,
            'error' => sprintf('Aucun élément trouvé avec le numéro "%s".', $serial),
        ];
    }

    return [
        'success' => true,
        'data' => [/* ... */],
    ];
}
```

### ✅ Sécurité

- **Ne jamais exposer** de données sensibles (mots de passe, tokens, numéros de carte)
- **Limiter les résultats** avec des paginations pour éviter les surcharges
- **Valider les entrées** — ne pas faire confiance aveuglément aux paramètres fournis par l'IA
- **Logger les appels** pour audit et débogage

### ✅ Performance

- Utiliser des requêtes Doctrine optimisées (jointures, index)
- Limiter le nombre de résultats (`setMaxResults`)
- Mettre en cache les résultats coûteux si nécessaire

---

## 11. Commandes utiles (antisèche)

| Action | Commande |
|--------|----------|
| Installer Mate | `docker compose exec php composer require --dev symfony/ai-mate` |
| Initialiser | `docker compose exec php vendor/bin/mate init` |
| Mettre à jour l'autoloader | `docker compose exec php composer dump-autoload` |
| Découvrir les extensions | `docker compose exec php vendor/bin/mate discover` |
| Lancer le serveur MCP | `docker compose exec php vendor/bin/mate serve` |
| Lister les outils | `docker compose exec php vendor/bin/mate mcp:tools:list` |
| Inspecter un outil | `docker compose exec php vendor/bin/mate mcp:tools:inspect <nom>` |
| Appeler un outil | `docker compose exec php vendor/bin/mate mcp:tools:call <nom> '<json>'` |
| Déboguer les capacités | `docker compose exec php vendor/bin/mate debug:capabilities` |
| Déboguer (tools uniquement) | `docker compose exec php vendor/bin/mate debug:capabilities --type=tool` |

---

## Ressources

- 📖 [Documentation officielle Symfony AI Mate](https://symfony.com/doc/current/ai/components/mate.html)
- 📖 [Créer des extensions MCP](https://symfony.com/doc/current/ai/components/mate/creating-extensions.html)
- 📦 [Packagist - symfony/ai-mate](https://packagist.org/packages/symfony/ai-mate)
- 🏗️ [Repository GitHub Symfony AI](https://github.com/symfony/ai)
