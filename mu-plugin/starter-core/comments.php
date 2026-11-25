<?php
/**
 * Désactive complètement les commentaires (inutiles sur un site vitrine,
 * et source de spam).
 *
 * Pour les réactiver : retirer 'comments' de la liste dans starter-core.php.
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', 'starter_disable_comments_support', 100 );

/**
 * Retire le support des commentaires et pings de tous les types de contenu.
 */
function starter_disable_comments_support(): void {
	foreach ( get_post_types() as $post_type ) {
		if ( post_type_supports( $post_type, 'comments' ) ) {
			remove_post_type_support( $post_type, 'comments' );
			remove_post_type_support( $post_type, 'trackbacks' );
		}
	}
}

// Fermés partout, y compris sur les anciens contenus.
add_filter( 'comments_open', '__return_false', 20 );
add_filter( 'pings_open', '__return_false', 20 );

// Aucun commentaire existant n'est affiché.
add_filter( 'comments_array', '__return_empty_array', 10 );

add_action( 'admin_menu', 'starter_disable_comments_admin_menu' );

/**
 * Retire le menu « Commentaires » et la page de réglages « Discussion ».
 */
function starter_disable_comments_admin_menu(): void {
	remove_menu_page( 'edit-comments.php' );
	remove_submenu_page( 'options-general.php', 'options-discussion.php' );
}

add_action( 'admin_init', 'starter_disable_comments_admin_pages' );

/**
 * Redirige si on tente d'ouvrir la page des commentaires et retire le widget du tableau de bord.
 */
function starter_disable_comments_admin_pages(): void {
	global $pagenow;

	if ( in_array( $pagenow, array( 'edit-comments.php', 'options-discussion.php' ), true ) ) {
		wp_safe_redirect( admin_url() );
		exit;
	}

	remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
}

add_action( 'admin_bar_menu', 'starter_disable_comments_admin_bar', 999 );

/**
 * Retire l'icône des commentaires de la barre d'admin.
 *
 * @param WP_Admin_Bar $bar Barre d'admin.
 */
function starter_disable_comments_admin_bar( WP_Admin_Bar $bar ): void {
	$bar->remove_node( 'comments' );
}

// Plus de lien vers le flux RSS des commentaires.
add_filter( 'feed_links_show_comments_feed', '__return_false' );
