<?php
/**
 * Configuration de base du thème : ce que WordPress doit activer.
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

add_action( 'after_setup_theme', 'starter_setup' );

/**
 * Déclare les fonctionnalités supportées par le thème.
 */
function starter_setup(): void {
	// Traductions du thème (dossier languages/, à créer si besoin).
	load_theme_textdomain( 'starter', STARTER_DIR . '/languages' );

	// WordPress gère la balise <title> (plus besoin de l'écrire dans header.php).
	add_theme_support( 'title-tag' );

	// Logo personnalisable dans Apparence > Personnaliser > Identité du site.
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 96,
			'width'       => 320,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	// Image mise en avant sur les articles et pages.
	add_theme_support( 'post-thumbnails' );

	// Balisage HTML5 propre pour les éléments générés par WordPress.
	add_theme_support(
		'html5',
		array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);

	// Les vidéos intégrées (YouTube…) gardent leurs proportions.
	add_theme_support( 'responsive-embeds' );

	// Emplacements de menus, remplis dans Apparence > Menus.
	register_nav_menus(
		array(
			'primary' => __( 'Menu principal', 'starter' ),
			'footer'  => __( 'Menu du pied de page', 'starter' ),
		)
	);
}
