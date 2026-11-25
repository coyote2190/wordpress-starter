<?php
/**
 * Nettoyage : retire ce que WordPress ajoute par défaut et dont on n'a pas besoin.
 *
 * Chaque ligne est commentée : pour réactiver un élément, supprimer la ligne.
 * Ce qui est volontairement CONSERVÉ est listé dans docs/cleanup.md.
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', 'starter_cleanup_head' );

/**
 * Retire les balises et scripts inutiles du <head> et du pied de page.
 */
function starter_cleanup_head(): void {
	// Emojis : WordPress charge un script + un CSS pour remplacer les emojis
	// par des images. Les navigateurs modernes les affichent très bien seuls.
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_enqueue_scripts', 'wp_enqueue_emoji_styles' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	add_filter( 'emoji_svg_url', '__return_false' );

	// <meta name="generator" content="WordPress x.y"> : donne la version aux robots.
	remove_action( 'wp_head', 'wp_generator' );

	// Lien RSD (pour d'anciens logiciels d'édition à distance).
	remove_action( 'wp_head', 'rsd_link' );

	// Lien court ?p=123 (dans le <head> et dans les en-têtes HTTP).
	remove_action( 'wp_head', 'wp_shortlink_wp_head', 10 );
	remove_action( 'template_redirect', 'wp_shortlink_header', 11 );

	// Liens de découverte de l'API REST (l'API reste active, seuls les liens disparaissent).
	remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
	remove_action( 'template_redirect', 'rest_output_link_header', 11 );

	// oEmbed : permet à d'autres sites d'intégrer nos pages en iframe. Inutile pour un site vitrine.
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 4 );
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
	remove_action( 'wp_head', 'wp_oembed_add_host_js' );

	// Flux RSS des commentaires et des catégories (le flux principal est gardé).
	remove_action( 'wp_head', 'feed_links_extra', 3 );
}

add_action( 'wp_enqueue_scripts', 'starter_cleanup_styles', 20 );

/**
 * Retire les feuilles de style WordPress inutiles côté visiteur.
 */
function starter_cleanup_styles(): void {
	// Styles « thème classique » : couleurs par défaut des boutons et fichiers
	// Gutenberg. On style ces blocs nous-mêmes avec Tailwind.
	wp_dequeue_style( 'classic-theme-styles' );
}

// CSS des blocs Gutenberg : ne charger que le CSS des blocs présents dans
// la page, au lieu de la feuille complète de tous les blocs.
add_filter( 'should_load_separate_core_block_assets', '__return_true' );
