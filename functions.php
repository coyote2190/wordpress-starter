<?php

/**
 * WordPress Starter — functions.php
 * Point d'entrée du thème : n'inclut que les fichiers de inc/
 */

if (!defined('ABSPATH')) {
    exit(); // Sécurité : empêche l'accès direct au fichier
}

define('STARTER_THEME_DIR', get_template_directory());
define('STARTER_THEME_URI', get_template_directory_uri());
define('STARTER_VERSION', '0.1.0');

// Chargement des modules du thème
require_once STARTER_THEME_DIR . '/inc/setup.php';
require_once STARTER_THEME_DIR . '/inc/enqueue.php';
require_once STARTER_THEME_DIR . '/inc/helpers.php';

// Décommente au fur et à mesure que tu ajoutes ces fichiers :
// require_once STARTER_THEME_DIR . '/inc/customizer.php';
// require_once STARTER_THEME_DIR . '/inc/acf-fields.php';
require_once STARTER_THEME_DIR . '/inc/custom-post-types.php';

if (class_exists('ACF')) {
    require_once STARTER_THEME_DIR . '/inc/acf.php';
}
