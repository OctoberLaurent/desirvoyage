# E2E Testing Documentation with Cypress

Cypress is the tool chosen in this project to perform End-to-End (E2E) testing. To ensure a perfectly consistent test environment for everyone and to avoid compatibility issues (especially on Macs with ARM/Apple Silicon chips), Cypress is entirely managed through **Docker**.

## Case 1: Launching the Graphical Interface (Development Mode)

This is the recommended approach when you are writing tests or want to visually oversee how the tests interact with the application.

We use a special container (`cypress-vnc`) that embeds a virtual screen (VNC) accessible straight from your native web browser.

**1. Start the Cypress container**
Open your terminal at the root of the project and run the dedicated `make` command:
```bash
make cypress
```

**2. View the Cypress Interface**
Open your preferred web browser (Chrome, Firefox, Safari...) and go to this URL:
👉 **[http://localhost:6080/vnc.html](http://localhost:6080/vnc.html)**

**3. Run your tests**
- If prompted, hit the "Connect" button (it usually handles this automatically).
- Wait a few moments: the Cypress Launchpad dashboard will appear on screen.
- Select the **E2E Testing** option.
- Choose the suggested browser (typically **Electron**).
- You will see a list of your test specs (e.g., `home.cy.js`). Click on a test to execute it visually.

**4. Stop the environment**
When you are done testing:
```bash
# Gracefully power off the background process
make cypress-stop
# (Or simply use "make stop" to turn off the entire project)
```

---

## Case 2: Running tests in the background (Headless Mode / CI)

If you simply want the machine to run the entire test suite mutely to check if everything works (without any graphical interface), use the standard Cypress container.

```bash
make cypress-headless
```
Cypress will execute all the specs located in the `/cypress/e2e` folder and print a detailed pass/fail report directly inside your terminal.

---

## Case 3: Launching the Graphical Interface Natively (Without VNC)

If you don't want to use VNC and prefer Cypress to open as a standard application on your computer (utilizing your own native Chrome/Safari browser), you can launch it natively.

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
