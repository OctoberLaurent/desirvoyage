describe('Page d\'inscription', () => {
    beforeEach(() => {
        cy.visit('/register');
    });

    it('Affiche le formulaire d\'inscription', () => {
        cy.get('form').should('exist');
        cy.get('input[name$="[lastname]"]').should('exist');
        cy.get('input[name$="[firstname]"]').should('exist');
        cy.get('input[name$="[birthday]"]').should('exist');
        cy.get('input[name$="[address]"]').should('exist');
        cy.get('input[name$="[postalCode]"]').should('exist');
        cy.get('input[name$="[city]"]').should('exist');
        cy.get('select[name$="[country]"]').should('exist');
        cy.get('input[name$="[phone]"]').should('exist');
        cy.get('input[name$="[email]"]').should('exist');
        cy.get('input[name$="[password][first]"]').should('exist');
        cy.get('input[name$="[password][second]"]').should('exist');
        cy.get('input[name$="[agreeTerms]"]').should('exist');
        cy.get('button[type="submit"]').should('exist');
    });

    it('Inscrit un utilisateur avec succès', () => {
        cy.fixture('user').then((user) => {
            // Générer un email unique pour éviter le conflit UniqueEntity
            const uniqueEmail = `test-cypress-${Date.now()}@example.com`;

            cy.get('input[name$="[lastname]"]').type(user.lastname, { force: true });
            cy.get('input[name$="[firstname]"]').type(user.firstname, { force: true });
            cy.get('input[name$="[birthday]"]').type(user.birthday, { force: true });
            cy.get('input[name$="[address]"]').type(user.address, { force: true });
            cy.get('input[name$="[postalCode]"]').type(user.postalCode, { force: true });
            cy.get('input[name$="[city]"]').type(user.city, { force: true });
            cy.get('select[name$="[country]"]').invoke('val', user.country).trigger('change', { force: true });
            cy.get('input[name$="[phone]"]').type(user.phone, { force: true });
            cy.get('input[name$="[email]"]').type(uniqueEmail, { force: true });
            cy.get('input[name$="[password][first]"]').type(user.password, { force: true });
            cy.get('input[name$="[password][second]"]').type(user.password, { force: true });
            cy.get('input[name$="[agreeTerms]"]').check({ force: true });

            cy.get('button[type="submit"]').click({ force: true });

            // Vérifie la redirection vers /login
            cy.url().should('include', '/login');

            // Vérifie le message flash de succès
            cy.get('.green, .accent-3, [class*="green"]').should('contain', 'créé');
        });
    });

    it('Refuse l\'inscription avec des champs manquants', () => {
        cy.get('button[type="submit"]').click({ force: true });

        // Vérifie que des erreurs sont affichées
        cy.get('.errors, .red-text').should('exist');
    });

    it('Refuse l\'inscription avec un email invalide', () => {
        cy.fixture('user').then((user) => {
            cy.get('input[name$="[lastname]"]').type(user.lastname, { force: true });
            cy.get('input[name$="[firstname]"]').type(user.firstname, { force: true });
            cy.get('input[name$="[birthday]"]').type(user.birthday, { force: true });
            cy.get('input[name$="[address]"]').type(user.address, { force: true });
            cy.get('input[name$="[postalCode]"]').type(user.postalCode, { force: true });
            cy.get('input[name$="[city]"]').type(user.city, { force: true });
            cy.get('select[name$="[country]"]').invoke('val', user.country).trigger('change', { force: true });
            cy.get('input[name$="[phone]"]').type(user.phone, { force: true });
            cy.get('input[name$="[email]"]').type('email-invalide', { force: true });
            cy.get('input[name$="[password][first]"]').type(user.password, { force: true });
            cy.get('input[name$="[password][second]"]').type(user.password, { force: true });
            cy.get('input[name$="[agreeTerms]"]').check({ force: true });

            cy.get('button[type="submit"]').click({ force: true });

            // L'URL ne doit pas changer (reste sur /register)
            cy.url().should('include', '/register');
        });
    });

    it('Refuse l\'inscription avec des mots de passe différents', () => {
        cy.fixture('user').then((user) => {
            cy.get('input[name$="[lastname]"]').type(user.lastname, { force: true });
            cy.get('input[name$="[firstname]"]').type(user.firstname, { force: true });
            cy.get('input[name$="[birthday]"]').type(user.birthday, { force: true });
            cy.get('input[name$="[address]"]').type(user.address, { force: true });
            cy.get('input[name$="[postalCode]"]').type(user.postalCode, { force: true });
            cy.get('input[name$="[city]"]').type(user.city, { force: true });
            cy.get('select[name$="[country]"]').invoke('val', user.country).trigger('change', { force: true });
            cy.get('input[name$="[phone]"]').type(user.phone, { force: true });
            cy.get('input[name$="[email]"]').type(user.email, { force: true });
            cy.get('input[name$="[password][first]"]').type(user.password, { force: true });
            cy.get('input[name$="[password][second]"]').type('Different1!', { force: true });
            cy.get('input[name$="[agreeTerms]"]').check({ force: true });

            cy.get('button[type="submit"]').click({ force: true });

            // Vérifie l'erreur de mots de passe non identiques
            cy.get('.errors, .red-text').should('exist');
            cy.url().should('include', '/register');
        });
    });

    it('Refuse l\'inscription avec un mot de passe trop faible', () => {
        cy.fixture('user').then((user) => {
            cy.get('input[name$="[lastname]"]').type(user.lastname, { force: true });
            cy.get('input[name$="[firstname]"]').type(user.firstname, { force: true });
            cy.get('input[name$="[birthday]"]').type(user.birthday, { force: true });
            cy.get('input[name$="[address]"]').type(user.address, { force: true });
            cy.get('input[name$="[postalCode]"]').type(user.postalCode, { force: true });
            cy.get('input[name$="[city]"]').type(user.city, { force: true });
            cy.get('select[name$="[country]"]').invoke('val', user.country).trigger('change', { force: true });
            cy.get('input[name$="[phone]"]').type(user.phone, { force: true });
            cy.get('input[name$="[email]"]').type(user.email, { force: true });
            cy.get('input[name$="[password][first]"]').type('weak', { force: true });
            cy.get('input[name$="[password][second]"]').type('weak', { force: true });
            cy.get('input[name$="[agreeTerms]"]').check({ force: true });

            cy.get('button[type="submit"]').click({ force: true });

            // Vérifie l'erreur de mot de passe invalide
            cy.get('.errors, .red-text').should('exist');
            cy.url().should('include', '/register');
        });
    });
});