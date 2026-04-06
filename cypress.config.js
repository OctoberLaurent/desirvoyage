const { defineConfig } = require('cypress');

module.exports = defineConfig({
    e2e: {
        baseUrl: process.env.CYPRESS_BASE_URL || 'http://localhost:8888',
        specPattern: 'cypress/e2e/**/*.cy.{js,jsx,ts,tsx}',
        viewportWidth: 1280,
        viewportHeight: 720,
        video: true,
        screenshotOnRunFailure: true,
        defaultCommandTimeout: 10000,
        requestTimeout: 10000,
        responseTimeout: 30000,
    },
});