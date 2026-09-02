<?php
/**
 * Configuration ACF
 */

if (!defined('ABSPATH')) {
    exit();
}

/**
 * Sauvegarde des groupes de champs en JSON dans le thème
 */
add_filter('acf/settings/save_json', function () {
    return STARTER_THEME_DIR . '/acf-json';
});

add_filter('acf/settings/load_json', function ($paths) {
    return [STARTER_THEME_DIR . '/acf-json'];
});
