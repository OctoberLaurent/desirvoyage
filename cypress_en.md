# E2E Testing Documentation with Cypress

Cypress is the tool chosen in this project to perform End-to-End (E2E) testing. To ensure a perfectly consistent test environment for everyone and to avoid compatibility issues (especially on Macs with ARM/Apple Silicon chips), Cypress is entirely managed through **Docker**.

---

## Case 1: Running tests in the background (Headless Mode / CI)

If you simply want the machine to run the entire test suite mutely to check if everything works (without any graphical interface), use the standard Cypress container.

```bash
make cypress-headless
```
Cypress will execute all the specs located in the `/cypress/e2e` folder and print a detailed pass/fail report directly inside your terminal.

---

## Case 2: Launching the Graphical Interface Natively

If you prefer Cypress to open as a standard application on your computer (utilizing your own native Chrome/Safari browser), you can launch it natively.

*(Requires NodeJS/NPM to be installed on your host machine).*

Run this from your root terminal:
```bash
make cypress-local
```
This will install/launch Cypress locally and open the interface directly on your host machine's screen!

---

## Where are the test files located?

All Cypress-related files (which you can edit seamlessly on your Mac) are structured at the root of the project:
- `cypress/e2e/` : This is where you write and define your test files (the `.cy.js` specs).
- `cypress/fixtures/` : To store static mock data (JSON files to be used inside tests).
- `cypress/support/` : Custom commands and global behaviors.
- `cypress.config.js` : The primary global Cypress configuration (including the system Base URL that points seamlessly to the Docker web server container).