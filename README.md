# 🌴 Désir de Voyage

> Application web de réservation de voyages — Symfony 7.4 + Docker  
> Travel booking web application — Symfony 7.4 + Docker

---

## 🇫🇷 Documentation Française

- [Présentation](#-présentation)
- [Prérequis](#-prérequis)
- [Installation](#-installation)
- [Démarrage](#-démarrage)
- [Commandes disponibles](#-commandes-disponibles)
- [Tests & Qualité du code](#-tests--qualité-du-code)

---

## 🇬🇧 English Documentation

- [Overview](#-overview)
- [Requirements](#-requirements)
- [Installation](#-installation-1)
- [Getting Started](#-getting-started)
- [Available Commands](#-available-commands)
- [Tests & Code Quality](#-tests--code-quality)

---

---

# 🇫🇷 Documentation Française

## 📖 Présentation

**Désir de Voyage** est une application web de réservation de voyages développée avec :

- **Symfony 7.4** (Framework PHP)
- **PHP 8.4**
- **MariaDB 10.5** (base de données)
- **Docker** (conteneurisation)
- **Webpack Encore** (assets front-end)
- **Stripe** (paiement en ligne)
- **EasyAdmin** (interface d'administration)

---

## ✅ Prérequis

Avant de commencer, assurez-vous d'avoir installé sur votre machine :

| Outil | Version minimale | Vérification |
|---|---|---|
| [Docker](https://www.docker.com/) | 20.x | `docker --version` |
| [Docker Compose](https://docs.docker.com/compose/) | 2.x | `docker compose version` |
| [Make](https://www.gnu.org/software/make/) | 3.x | `make --version` |
| [Git](https://git-scm.com/) | 2.x | `git --version` |

> **Note :** PHP, Composer et Symfony CLI **ne sont pas requis** sur votre machine hôte. Tout s'exécute à l'intérieur des conteneurs Docker.

---

## 🚀 Installation

### 1. Cloner le dépôt

```bash
git clone https://github.com/OctoberLaurent/desirvoyage.git
cd desirvoyage
```

### 2. Configurer les variables d'environnement

Copiez le fichier d'exemple et ajustez les valeurs selon votre environnement :

```bash
cp .env .env.local
```

Éditez `.env.local` et configurez notamment :

```dotenv
# URL de connexion à la base de données (ne pas modifier le host "db" pour Docker)
DATABASE_URL="mysql://symfony:symfony@db:3306/dock?serverVersion=10.5.8-MariaDB&charset=utf8mb4"

# Clés Stripe (paiement)
STRIPE_PUBLIC_KEY=pk_test_...
STRIPE_SECRET_KEY=sk_test_...

# Configuration du mailer
MAILER_DSN=smtp://...
```

> ⚠️ **Important :** Ne modifiez pas le host `db` dans `DATABASE_URL`. C'est le nom du service Docker défini dans `docker-compose.yml`.

### 3. Initialiser le projet complet

Cette commande **unique** construit les images Docker, installe les dépendances Composer, crée la base de données et applique les migrations :

```bash
make setup
```

### 4. (Optionnel) Charger les fixtures de démonstration

```bash
make fixtures
```

### 5. (Optionnel) Importer un dump SQL existant

Si vous disposez d'un fichier `bdd.sql` à la racine du projet :

```bash
make db-import
```

---

## ▶️ Démarrage

### Lancer les conteneurs

```bash
make start
```

L'application sera disponible aux adresses suivantes :

| Service | URL |
|---|---|
| 🌐 Application web | http://localhost:8888 |
| 🗄️ PhpMyAdmin | http://localhost:8080 |
| 🐘 PHP-FPM | localhost:9000 |
| 🛢️ MariaDB | localhost:8889 |

### Arrêter les conteneurs

```bash
make stop
```

### Supprimer les conteneurs et le réseau

```bash
make down
```

### Accéder au shell du conteneur PHP

```bash
make shell
```

---

## 📋 Commandes disponibles

Toutes les commandes disponibles sont listées via :

```bash
make help
```

| Commande | Description |
|---|---|
| `make start` | Démarrer les conteneurs Docker |
| `make stop` | Arrêter les conteneurs Docker |
| `make down` | Supprimer les conteneurs et le réseau |
| `make restart` | Redémarrer les conteneurs |
| `make build` | Reconstruire les images Docker (sans cache) |
| `make logs` | Afficher les logs en temps réel |
| `make shell` | Accéder au terminal du conteneur PHP |
| `make cc` | Vider le cache Symfony |
| `make setup` | Initialisation complète du projet |
| `make migrate` | Lancer les migrations Doctrine |
| `make fixtures` | Charger les fixtures de données |
| `make db-import` | Importer un fichier `bdd.sql` |
| `make cs-fix` | Corriger le style du code (PHP CS Fixer) |
| `make phpstan` | Analyse statique avec PHPStan |
| `make psalm` | Analyse statique avec Psalm |

---

## 🧪 Tests & Qualité du code

Le projet dispose de plusieurs niveaux de validation de code.

### 1. Tests unitaires et fonctionnels — PHPUnit

Les tests sont situés dans le répertoire `tests/`.

**Lancer tous les tests :**

```bash
docker exec -it php-fpm php bin/phpunit
```

**Lancer les tests avec couverture de code :**

```bash
docker exec -it php-fpm php bin/phpunit --coverage-text
```

**Lancer un fichier de test spécifique :**

```bash
docker exec -it php-fpm php bin/phpunit tests/Controllers/MonTest.php
```

> La configuration PHPUnit se trouve dans `phpunit.xml.dist`.

---

### 2. Analyse statique — PHPStan

PHPStan analyse le code PHP à la recherche d'erreurs potentielles sans exécuter le code.

```bash
make phpstan
```

Ou directement :

```bash
docker exec -it php-fpm vendor/bin/phpstan analyse --memory-limit=1G
```

> La configuration PHPStan se trouve dans `phpstan.dist.neon`.

---

### 3. Analyse statique — Psalm

Psalm est un outil d'analyse de type statique complémentaire à PHPStan.

```bash
make psalm
```

Ou directement :

```bash
docker exec -it php-fpm vendor/bin/psalm
```

> La configuration Psalm se trouve dans `psalm.xml`.

---

### 4. Style du code — PHP CS Fixer

PHP CS Fixer applique automatiquement les standards de codage PSR-12.

**Corriger automatiquement le code :**

```bash
make cs-fix
```

Ou directement :

```bash
docker exec -it php-fpm vendor/bin/php-cs-fixer fix
```

**Vérifier sans modifier (mode dry-run) :**

```bash
docker exec -it php-fpm vendor/bin/php-cs-fixer fix --dry-run --diff
```

> La configuration PHP CS Fixer se trouve dans `.php-cs-fixer.dist.php`.

---

### Récapitulatif — Lancer tous les outils de qualité

```bash
# Tests PHPUnit
docker exec -it php-fpm php bin/phpunit

# Analyse statique PHPStan
make phpstan

# Analyse statique Psalm
make psalm

# Correction du style de code
make cs-fix
```

---

---

# 🇬🇧 English Documentation

## 📖 Overview

**Désir de Voyage** is a travel booking web application built with:

- **Symfony 7.4** (PHP Framework)
- **PHP 8.4**
- **MariaDB 10.5** (database)
- **Docker** (containerization)
- **Webpack Encore** (front-end assets)
- **Stripe** (online payments)
- **EasyAdmin** (administration interface)

---

## ✅ Requirements

Before you begin, make sure you have the following installed on your machine:

| Tool | Minimum version | Check |
|---|---|---|
| [Docker](https://www.docker.com/) | 20.x | `docker --version` |
| [Docker Compose](https://docs.docker.com/compose/) | 2.x | `docker compose version` |
| [Make](https://www.gnu.org/software/make/) | 3.x | `make --version` |
| [Git](https://git-scm.com/) | 2.x | `git --version` |

> **Note:** PHP, Composer and Symfony CLI are **not required** on your host machine. Everything runs inside Docker containers.

---

## 🚀 Installation

### 1. Clone the repository

```bash
git clone https://github.com/OctoberLaurent/desirvoyage.git
cd desirvoyage
```

### 2. Configure environment variables

Copy the sample file and adjust the values to your environment:

```bash
cp .env .env.local
```

Edit `.env.local` and configure in particular:

```dotenv
# Database connection URL (do NOT change the "db" host for Docker)
DATABASE_URL="mysql://symfony:symfony@db:3306/dock?serverVersion=10.5.8-MariaDB&charset=utf8mb4"

# Stripe keys (payment)
STRIPE_PUBLIC_KEY=pk_test_...
STRIPE_SECRET_KEY=sk_test_...

# Mailer configuration
MAILER_DSN=smtp://...
```

> ⚠️ **Important:** Do not change the `db` host in `DATABASE_URL`. It is the name of the Docker service defined in `docker-compose.yml`.

### 3. Initialize the full project

This **single command** builds the Docker images, installs Composer dependencies, creates the database and runs the migrations:

```bash
make setup
```

### 4. (Optional) Load demo fixtures

```bash
make fixtures
```

### 5. (Optional) Import an existing SQL dump

If you have a `bdd.sql` file at the project root:

```bash
make db-import
```

---

## ▶️ Getting Started

### Start the containers

```bash
make start
```

The application will be available at the following addresses:

| Service | URL |
|---|---|
| 🌐 Web Application | http://localhost:8888 |
| 🗄️ PhpMyAdmin | http://localhost:8080 |
| 🐘 PHP-FPM | localhost:9000 |
| 🛢️ MariaDB | localhost:8889 |

### Stop the containers

```bash
make stop
```

### Remove containers and network

```bash
make down
```

### Access the PHP container shell

```bash
make shell
```

---

## 📋 Available Commands

All available commands are listed via:

```bash
make help
```

| Command | Description |
|---|---|
| `make start` | Start Docker containers |
| `make stop` | Stop Docker containers |
| `make down` | Remove containers and network |
| `make restart` | Restart containers |
| `make build` | Rebuild Docker images (no cache) |
| `make logs` | Show real-time logs |
| `make shell` | Access the PHP container terminal |
| `make cc` | Clear Symfony cache |
| `make setup` | Full project initialization |
| `make migrate` | Run Doctrine migrations |
| `make fixtures` | Load data fixtures |
| `make db-import` | Import a `bdd.sql` file |
| `make cs-fix` | Fix code style (PHP CS Fixer) |
| `make phpstan` | Static analysis with PHPStan |
| `make psalm` | Static analysis with Psalm |

---

## 🧪 Tests & Code Quality

The project has several levels of code validation.

### 1. Unit and Functional Tests — PHPUnit

Tests are located in the `tests/` directory.

**Run all tests:**

```bash
docker exec -it php-fpm php bin/phpunit
```

**Run tests with code coverage:**

```bash
docker exec -it php-fpm php bin/phpunit --coverage-text
```

**Run a specific test file:**

```bash
docker exec -it php-fpm php bin/phpunit tests/Controllers/MyTest.php
```

> PHPUnit configuration is located in `phpunit.xml.dist`.

---

### 2. Static Analysis — PHPStan

PHPStan analyzes PHP code for potential errors without executing it.

```bash
make phpstan
```

Or directly:

```bash
docker exec -it php-fpm vendor/bin/phpstan analyse --memory-limit=1G
```

> PHPStan configuration is located in `phpstan.dist.neon`.

---

### 3. Static Analysis — Psalm

Psalm is a static type analysis tool that complements PHPStan.

```bash
make psalm
```

Or directly:

```bash
docker exec -it php-fpm vendor/bin/psalm
```

> Psalm configuration is located in `psalm.xml`.

---

### 4. Code Style — PHP CS Fixer

PHP CS Fixer automatically applies PSR-12 coding standards.

**Automatically fix the code:**

```bash
make cs-fix
```

Or directly:

```bash
docker exec -it php-fpm vendor/bin/php-cs-fixer fix
```

**Check without modifying (dry-run mode):**

```bash
docker exec -it php-fpm vendor/bin/php-cs-fixer fix --dry-run --diff
```

> PHP CS Fixer configuration is located in `.php-cs-fixer.dist.php`.

---

### Summary — Run all quality tools

```bash
# PHPUnit tests
docker exec -it php-fpm php bin/phpunit

# PHPStan static analysis
make phpstan

# Psalm static analysis
make psalm

# Code style fix
make cs-fix
```

---

## 🏗️ Project Structure

```
desirvoyage/
├── assets/             # Front-end files (JS, SCSS)
├── config/             # Symfony configuration
├── docker-symfony/     # Docker configuration (Nginx, PHP-FPM, MariaDB)
├── migrations/         # Doctrine migration files
├── public/             # Web root (index.php, uploaded images)
├── src/
│   ├── Command/        # Symfony Console commands
│   ├── Controller/     # HTTP Controllers
│   ├── Controller/Admin/ # EasyAdmin controllers
│   ├── DataFixtures/   # Data fixtures
│   ├── Entity/         # Doctrine entities
│   ├── Form/           # Symfony form types
│   ├── Repository/     # Doctrine repositories
│   ├── Security/       # Authentication & authorization
│   ├── Service/        # Business logic services
│   └── Twig/           # Twig extensions
├── templates/          # Twig templates
├── tests/              # PHPUnit tests
├── translations/       # Translation files (fr)
├── .php-cs-fixer.dist.php  # PHP CS Fixer config
├── phpstan.dist.neon   # PHPStan config
├── psalm.xml           # Psalm config
├── phpunit.xml.dist    # PHPUnit config
├── Makefile            # Automation commands
└── compose.yaml        # Docker Compose (root)
```

---

## 🔑 Default Credentials

### Database (Docker)

| Parameter | Value |
|---|---|
| Host | `db` (inside Docker) / `localhost:8889` (outside) |
| Database | `dock` |
| User | `symfony` |
| Password | `symfony` |
| Root password | `root` |

---

## 📄 License

Ce projet est sous licence propriétaire. Tous droits réservés.  
This project is under a proprietary license. All rights reserved.

---

*Built with ❤️ using Symfony 7.4*
