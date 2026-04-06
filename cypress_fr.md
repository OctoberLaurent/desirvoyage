# Documentation des Tests E2E avec Cypress

Cypress est l'outil utilisé dans ce projet pour réaliser les tests de bout en bout (End-to-End). Afin de garantir un environnement de test identique pour tout le monde et éviter les problèmes de compatibilité (particulièrement sur Mac avec puces ARM/Apple Silicon), l'environnement Cypress est entièrement géré à travers **Docker**.

## Cas 1 : Lancer l'Interface Graphique (Mode Développement)

C'est la méthode recommandée lorsque vous écrivez des tests ou que vous souhaitez voir visuellement comment les tests interagissent avec votre site.

Nous utilisons un conteneur spécial (`cypress-vnc`) qui intègre un serveur visuel (VNC) accessible directement depuis votre propre navigateur.

**1. Démarrer le conteneur Cypress**
Ouvrez votre terminal, placez-vous à la racine du projet et lancez la commande `make` dédiée :
```bash
make cypress
```

**2. Visualiser l'interface de Cypress**
Ouvrez votre navigateur (Chrome, Firefox, Safari...) et accédez à cette URL :
👉 **[http://localhost:6080/vnc.html](http://localhost:6080/vnc.html)**

**3. Lancer vos tests**
- Si on vous le demande, cliquez sur le bouton "Connect" (parfois cela se fait automatiquement).
- Patientez quelques secondes : le Launchpad de Cypress va s'afficher à l'écran.
- Sélectionnez l'option **E2E Testing**.
- Choisissez le navigateur proposé (généralement **Electron**).
- Vous verrez la liste de vos tests (exemple : `home.cy.js`). Cliquez sur un test pour l'exécuter visuellement.

**4. Arrêter l'environnement**
Une fois que vous avez fini de tester :
```bash
# S'assure que le processus tourne en tâche de fond est arrêté proprement
make cypress-stop
# (Ou utilisez simplement "make stop" pour arrêter tout le projet)
```

---

## Cas 2 : Lancer les tests en arrière-plan (Mode Headless / Intégration Continue)

Si vous voulez juste que la machine exécute l'intégralité des tests invisibles pour vérifier que tout fonctionne (sans interface graphique), vous pouvez utiliser le conteneur Cypress standard.

```bash
make cypress-headless
```
Cypress va exécuter tous les specs situés dans le dossier `/cypress/e2e` et vous générer un rapport directement dans votre terminal.

---

## Cas 3 : Lancer l'Interface Graphique Nativement (Sans VNC)

Si ne voulez pas utiliser VNC et que vous préférez que Cypress s'ouvre comme un programme normal sur votre ordinateur (et utilise votre propre navigateur Chrome/Safari), vous pouvez le lancer nativement.

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
- `cypress/fixtures/` : Pour stocker des donnes statiques (fichiers JSON manipulés par les tests).
- `cypress/support/` : Commandes personnalisées et paramètres globaux.
- `cypress.config.js` : La configuration globale principale de Cypress (incluant l'URL de base, qui pointe vers le conteneur du serveur web).
