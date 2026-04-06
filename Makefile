# Default goal
.DEFAULT_GOAL := help

# --- Variables ---
DOCKER_DIR        = docker-symfony
DOCKER_COMPOSE    = docker compose -f $(DOCKER_DIR)/docker-compose.yml
PHP_CONTAINER     = php-fpm
CONSOLE           = docker exec -it $(PHP_CONTAINER) php -d memory_limit=512M bin/console

# --- Styling ---
GREEN  := $(shell tput -Txterm setaf 2)
YELLOW := $(shell tput -Txterm setaf 3)
WHITE  := $(shell tput -Txterm setaf 7)
CYAN   := $(shell tput -Txterm setaf 6)
RESET  := $(shell tput -Txterm sgr0)

# --- Commands ---

.PHONY: help
help: ## Afficher l'aide
	@echo "$(CYAN)Usage: make <command>$(RESET)"
	@echo ""
	@echo "$(YELLOW)Available commands:$(RESET)"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  $(GREEN)%-15s$(RESET) %s\n", $$1, $$2}'

.PHONY: start
start: ## Démarrer les containers Docker
	@echo "$(GREEN)Démarrage des containers...$(RESET)"
	@$(DOCKER_COMPOSE) up -d
	@echo "$(CYAN)--------------------------------------------------$(RESET)"
	@echo "$(WHITE)Application : $(GREEN)http://localhost:8888$(RESET)"
	@echo "$(WHITE)PhpMyAdmin  : $(GREEN)http://localhost:8080$(RESET)"
	@echo "$(CYAN)--------------------------------------------------$(RESET)"

.PHONY: stop
stop: ## Arrêter les containers Docker
	@echo "$(YELLOW)Arrêt des containers...$(RESET)"
	@$(DOCKER_COMPOSE) stop
	@echo "$(YELLOW)Containers arrêtés.$(RESET)"

.PHONY: down
down: ## Supprimer les containers et le réseau
	@echo "$(YELLOW)Suppression des containers...$(RESET)"
	@$(DOCKER_COMPOSE) down
	@echo "$(YELLOW)Containers supprimés.$(RESET)"

.PHONY: restart
restart: stop start ## Redémarrer les containers

.PHONY: build
build: ## Reconstruire les images Docker
	@echo "$(GREEN)Reconstruction des images...$(RESET)"
	@$(DOCKER_COMPOSE) build --no-cache

.PHONY: logs
logs: ## Afficher les logs des containers
	@$(DOCKER_COMPOSE) logs -f

.PHONY: cc
cc: ## Vider le cache de l'application
	@echo "$(GREEN)Vérification des containers...$(RESET)"
	@$(DOCKER_COMPOSE) up -d --no-recreate
	@echo "$(GREEN)Nettoyage du cache...$(RESET)"
	@$(CONSOLE) cache:clear

.PHONY: shell
shell: ## Accéder au terminal du container PHP
	@docker exec -it $(PHP_CONTAINER) /bin/bash

.PHONY: setup
setup: build start ## Initialiser le projet (docker + composer)
	@echo "$(GREEN)Installation des dépendances composer...$(RESET)"
	@docker exec -it $(PHP_CONTAINER) composer install
	@echo "$(GREEN)Mise en place de la base de données...$(RESET)"
	@$(CONSOLE) doctrine:database:create --if-not-exists
	@$(CONSOLE) doctrine:migrations:migrate --no-interaction

.PHONY: migrate
migrate: ## Lancer les migrations de base de données
	@$(CONSOLE) doctrine:migrations:migrate --no-interaction

.PHONY: fixtures
fixtures: ## Charger les fixtures de base de données
	@$(CONSOLE) doctrine:fixtures:load --no-interaction

.PHONY: db-import
db-import: ## Importer le fichier bdd.sql
	@echo "$(GREEN)Importation de la base de données...$(RESET)"
	@docker exec -i db mysql -u root -proot dock < bdd.sql
	@echo "$(GREEN)Base de données importée.$(RESET)"

.PHONY: cs-fix
cs-fix: ## Lancer PHP CS Fixer pour corriger le code
	@echo "$(GREEN)Lancement de PHP CS Fixer...$(RESET)"
	@docker exec $(PHP_CONTAINER) vendor/bin/php-cs-fixer fix

.PHONY: phpstan
phpstan: ## Lancer PHPStan pour l'analyse statique
	@echo "$(GREEN)Lancement de PHPStan...$(RESET)"
	@docker exec $(PHP_CONTAINER) vendor/bin/phpstan analyse --memory-limit=1G

.PHONY: psalm
psalm: ## Lancer Psalm pour l'analyse statique
	@echo "$(GREEN)Lancement de Psalm...$(RESET)"
	@docker exec $(PHP_CONTAINER) vendor/bin/psalm

.PHONY: cypress-headless
cypress-headless: ## Lancer les tests E2E Cypress (headless, sans VNC)
	@echo "$(GREEN)Lancement des tests Cypress en mode invisible...$(RESET)"
	@$(DOCKER_COMPOSE) run --rm cypress

.PHONY: cypress-local
cypress-local: ## Ouvrir Cypress nativement sur votre machine (Sans VNC)
	@echo "$(GREEN)Ouverture de l'interface Cypress native...$(RESET)"
	@npx cypress open

