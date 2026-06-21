# Documentation des Tests E2E avec Cypress

Cypress est l'outil utilisé dans ce projet pour réaliser les tests de bout en bout (End-to-End). Afin de garantir un environnement de test identique pour tout le monde et éviter les problèmes de compatibilité (particulièrement sur Mac avec puces ARM/Apple Silicon), l'environnement Cypress est entièrement géré à travers **Docker**.

---

## Cas 1 : Lancer les tests en arrière-plan (Mode Headless / Intégration Continue)

Si vous voulez que la machine exécute l'intégralité des tests de manière invisible pour vérifier que tout fonctionne (sans interface graphique), vous pouvez utiliser le conteneur Cypress standard.

```bash
make cypress-headless
```
Cypress va exécuter tous les specs situés dans le dossier `/cypress/e2e` et vous générer un rapport directement dans votre terminal.

---

## Cas 2 : Lancer l'Interface Graphique Nativement

Si vous préférez que Cypress s'ouvre comme un programme normal sur votre ordinateur (et utilise votre propre navigateur Chrome/Safari), vous pouvez le lancer nativement.

*(Nécessite que NodeJS/NPM soit installé sur votre ordinateur).*

Exécutez dans votre terminal à la racine :
```bash
make cypress-local
```
Cela installera/lancera Cypress localement et ouvrira l'interface directement sur l'écran de votre machine !

---

## Où trouver les fichiers de tests ?

Tous les fichiers relatifs à Cypress (que vous pouvez modifier sur votre Mac) sont stockés à la racine de votre projet :
- `cypress/e2e/` : C'est ici que vous placez vos fichiers de tests (les specs en `.cy.js`).
- `cypress/fixtures/` : Pour stocker des données statiques (fichiers JSON manipulés par les tests).
- `cypress/support/` : Commandes personnalisées et paramètres globaux.
- `cypress.config.js` : La configuration globale principale de Cypress (incluant l'URL de base, qui pointe vers le conteneur du serveur web).