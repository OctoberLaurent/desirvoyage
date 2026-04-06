describe('Page d\'accueil', () => {
    beforeEach(() => {
        cy.visit('/');
    });

    it('Affiche la page d\'accueil avec succès', () => {
        cy.title().should('not.be.empty');
    });

    it('Contient des éléments de navigation', () => {
        cy.get('nav, header, .navbar, .nav').should('exist');
    });

    it('Vérifie que les assets CSS/JS sont chargés', () => {
        cy.get('link[rel="stylesheet"]').should('have.length.at.least', 1);
    });
});