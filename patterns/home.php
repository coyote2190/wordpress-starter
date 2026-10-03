<?php
/**
 * Title: Page d'accueil complète
 * Slug: starter/home
 * Categories: starter
 * Block Types: core/post-content
 * Post Types: page
 * Description: Bannière, services, simulateur de devis et appel à l'action. Proposée à la création d'une page.
 */

// Assemble les autres compositions : les modifier les met à jour ici aussi
foreach (['hero', 'services', 'simulator', 'cta'] as $starter_pattern) {
    require __DIR__ . '/' . $starter_pattern . '.php';
    echo "\n\n";
}
