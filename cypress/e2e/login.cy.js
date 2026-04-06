describe('Page de connexion', () => {
    beforeEach(() => {
        cy.visit('/login');
    });

    it('Affiche le formulaire de connexion', () => {
        cy.get('form').should('exist');
        cy.get('input[name="email"]').should('exist');
        cy.get('input[name="password"]').should('exist');
    });

    it('Refuse la connexion avec des identifiants invalides', () => {
        cy.get('input[name="email"]').type('invalid@test.fr', { force: true });
        cy.get('input[name="password"]').type('wrongpassword{enter}', { force: true });
        cy.url().should('include', '/login');
    });
});