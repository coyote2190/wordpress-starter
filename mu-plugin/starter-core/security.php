<?php
/**
 * Durcissement de sécurité : réduit la surface d'attaque sans gêner le client.
 *
 * Voir docs/security.md (avec les réglages à mettre dans wp-config.php).
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

/*
 * XML-RPC : ancienne API (applis mobiles d'avant 2015, pingbacks). Cible
 * favorite des attaques par force brute. L'API REST la remplace.
 */
add_filter( 'xmlrpc_enabled', '__return_false' );
add_filter( 'pings_open', '__return_false' );

add_filter( 'wp_headers', 'starter_security_headers' );

/**
 * En-têtes HTTP de sécurité sur les pages du site + retrait de X-Pingback.
 *
 * @param array<string, string> $headers En-têtes envoyés par WordPress.
 * @return array<string, string>
 */
function starter_security_headers( array $headers ): array {
	unset( $headers['X-Pingback'] );

	// Le navigateur ne devine pas le type d'un fichier (évite d'exécuter un faux .jpg).
	$headers['X-Content-Type-Options'] = 'nosniff';
	// Le site ne peut pas être affiché dans une iframe d'un autre site (clickjacking).
	$headers['X-Frame-Options'] = 'SAMEORIGIN';
	// Les autres sites ne voient que le domaine d'origine, pas l'URL complète.
	$headers['Referrer-Policy'] = 'strict-origin-when-cross-origin';
	// Pas d'accès caméra / micro / géolocalisation sans besoin explicite.
	$headers['Permissions-Policy'] = 'camera=(), microphone=(), geolocation=()';

	return $headers;
}

/*
 * Énumération des utilisateurs : sans ça, n'importe qui peut obtenir les
 * identifiants de connexion via /?author=1 ou /wp-json/wp/v2/users.
 */
// Priorité 1 : avant redirect_canonical() (priorité 10) qui transformerait
// ?author=1 en /author/identifiant/ et révélerait l'identifiant.
add_action( 'template_redirect', 'starter_block_author_enumeration', 1 );

/**
 * /?author=N et pages auteur (/author/…) → redirection vers l'accueil pour les visiteurs.
 * Un site vitrine n'a pas besoin de pages auteur.
 */
function starter_block_author_enumeration(): void {
	if ( ! is_user_logged_in() && ( isset( $_GET['author'] ) || is_author() ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		wp_safe_redirect( home_url( '/' ), 301 );
		exit;
	}
}

add_filter( 'rest_endpoints', 'starter_hide_rest_users' );

/**
 * Retire les routes REST des utilisateurs pour les visiteurs non connectés
 * (l'éditeur Gutenberg, connecté, y garde accès).
 *
 * @param array<string, mixed> $endpoints Routes REST.
 * @return array<string, mixed>
 */
function starter_hide_rest_users( array $endpoints ): array {
	if ( is_user_logged_in() ) {
		return $endpoints;
	}
	foreach ( array_keys( $endpoints ) as $route ) {
		if ( str_starts_with( $route, '/wp/v2/users' ) ) {
			unset( $endpoints[ $route ] );
		}
	}
	return $endpoints;
}

// Pas de liste des auteurs dans le plan du site (wp-sitemap.xml).
add_filter(
	'wp_sitemaps_add_provider',
	static fn( $provider, string $name ) => 'users' === $name ? false : $provider,
	10,
	2
);

/*
 * Connexion : message d'erreur identique que l'identifiant ou le mot de
 * passe soit faux (sinon on confirme à l'attaquant qu'un identifiant existe).
 */
add_filter(
	'login_errors',
	static fn() => __( 'Identifiant ou mot de passe incorrect.', 'starter' )
);
