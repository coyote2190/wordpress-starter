<?php
/**
 * Chargement des CSS et JS du thème côté visiteur.
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

add_action( 'wp_enqueue_scripts', 'starter_enqueue_assets' );

/**
 * Charge les points d'entrée Vite sur toutes les pages.
 */
function starter_enqueue_assets(): void {
	starter_vite_enqueue( 'starter', 'assets/src/css/main.css' );
	starter_vite_enqueue( 'starter', 'assets/src/js/main.js' );
}
