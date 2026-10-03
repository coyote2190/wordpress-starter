<?php
/**
 * Configuration des champs personnalisés — SCF (Secure Custom Fields) ou ACF
 *
 * SCF est un fork d'ACF maintenu par WordPress.org : même API (get_field…),
 * mêmes hooks acf/*, même format JSON. Le thème fonctionne avec l'un ou l'autre
 * (jamais les deux activés en même temps).
 */

if (!defined('ABSPATH')) {
    exit();
}

/**
 * Sauvegarde des groupes de champs en JSON dans le thème (acf-json/)
 * → les champs sont versionnés et synchronisés entre environnements
 *   (admin : Champs > Groupes de champs > « Synchronisation disponible »)
 */
add_filter('acf/settings/save_json', function () {
    return STARTER_THEME_DIR . '/acf-json';
});

add_filter('acf/settings/load_json', function ($paths) {
    return [STARTER_THEME_DIR . '/acf-json'];
});
