<?php
/**
 * Point d'entrée du thème.
 *
 * Ce fichier ne contient aucune logique : il définit quelques constantes
 * puis charge les fichiers de inc/, chacun responsable d'un seul sujet.
 * Pour ajouter une fonctionnalité : créer inc/mon-sujet.php et l'ajouter
 * à la liste ci-dessous.
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

define( 'STARTER_VERSION', wp_get_theme()->get( 'Version' ) );
define( 'STARTER_DIR', get_template_directory() );
define( 'STARTER_URI', get_template_directory_uri() );

$starter_includes = array(
	'setup',   // Fonctionnalités WordPress du thème, menus.
	'cleanup', // Retire ce que WordPress charge inutilement.
	'vite',    // Pont Vite ↔ WordPress (dev / prod).
	'assets',  // Chargement des CSS et JS.
);

foreach ( $starter_includes as $starter_file ) {
	require_once STARTER_DIR . '/inc/' . $starter_file . '.php';
}
unset( $starter_includes, $starter_file );
