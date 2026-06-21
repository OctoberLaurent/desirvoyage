// Cypress support file - chargé avant chaque fichier de test
// Ajouter ici les commandes personnalisées et configurations globales

// Ignorer les erreurs JS non interceptées provenant de l'application (ex: Materialize autocomplete)
Cypress.on('uncaught:exception', (err) => {
    // Erreur Materialize : "Cannot set properties of null (setting 'tabIndex')"
    if (err.message.includes("Cannot set properties of null") && err.message.includes("tabIndex")) {
        return false; // ne pas faire échouer le test
    }
    // Laisser Cypress échouer pour les autres erreurs
    return true;
});
