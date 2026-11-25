<?php
/**
 * Plugin Name: Starter Core
 * Description: Fonctionnalités du site indépendantes du thème : types de contenu, taxonomies, formulaire de contact, désactivation des commentaires.
 * Version:     0.1.0
 * Text Domain: starter
 *
 * MU-plugin (« must-use ») : chargé automatiquement par WordPress, avant le
 * thème, et impossible à désactiver depuis l'admin.
 *
 * Installation : ce fichier ET le dossier starter-core/ vont dans
 * wp-content/mu-plugins/. Voir docs/mu-plugin.md.
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

$starter_core_files = array(
	'post-types', // Types de contenu et taxonomies.
	'comments',   // Désactive complètement les commentaires.
);

foreach ( $starter_core_files as $starter_core_file ) {
	require_once __DIR__ . '/starter-core/' . $starter_core_file . '.php';
}
unset( $starter_core_files, $starter_core_file );
