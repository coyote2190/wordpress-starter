<?php
/**
 * Intégration de Secure Custom Fields (SCF).
 *
 * - avertit dans l'admin si l'extension n'est pas active ;
 * - enregistre les groupes de champs en JSON dans scf-json/ (versionnés avec Git) ;
 * - crée la page d'options « Infos du site » ;
 * - fournit starter_field() / starter_option(), qui ne plantent pas si SCF est désactivé.
 *
 * SCF reprend l'API d'ACF : les fonctions et hooks s'appellent toujours acf_* / get_field().
 * Voir docs/scf.md.
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

/**
 * SCF est-il actif ?
 */
function starter_scf_active(): bool {
	return function_exists( 'get_field' );
}

add_action( 'admin_notices', 'starter_scf_missing_notice' );

/**
 * Message dans l'admin si SCF n'est pas actif.
 */
function starter_scf_missing_notice(): void {
	if ( starter_scf_active() || ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	printf(
		'<div class="notice notice-error"><p>%s</p></div>',
		esc_html__( 'Le thème a besoin de l\'extension « Secure Custom Fields ». Installez-la et activez-la dans Extensions > Ajouter.', 'starter' )
	);
}

/*
 * Local JSON : chaque fois qu'un groupe de champs est enregistré dans l'admin,
 * SCF écrit un fichier JSON dans scf-json/. On le commite : les champs sont
 * versionnés et se retrouvent sur n'importe quel site qui utilise le thème.
 */
add_filter(
	'acf/settings/save_json',
	static fn() => STARTER_DIR . '/scf-json'
);

add_filter(
	'acf/settings/load_json',
	static function ( array $paths ): array {
		// On remplace le dossier par défaut (acf-json/) par le nôtre.
		return array( STARTER_DIR . '/scf-json' );
	}
);

add_action( 'acf/init', 'starter_scf_options_pages' );

/**
 * Pages d'options : réglages globaux du site, modifiables par le client.
 * Les champs sont dans scf-json/group_starter_site_settings.json.
 */
function starter_scf_options_pages(): void {
	if ( ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}

	acf_add_options_page(
		array(
			'page_title' => __( 'Infos du site', 'starter' ),
			'menu_title' => __( 'Infos du site', 'starter' ),
			'menu_slug'  => 'starter-site-settings',
			'capability' => 'edit_pages', // Éditeurs et administrateurs.
			'icon_url'   => 'dashicons-store',
			'position'   => 59,
			'redirect'   => false,
		)
	);
}

/**
 * Valeur d'un champ SCF du contenu courant (ou d'un autre).
 *
 * @param string    $name    Nom du champ.
 * @param int|false $post_id ID du contenu, false = contenu courant.
 * @param mixed     $default Valeur si le champ est vide ou si SCF est inactif.
 * @return mixed
 */
function starter_field( string $name, int|false $post_id = false, mixed $default = '' ): mixed {
	if ( ! starter_scf_active() ) {
		return $default;
	}
	$value = get_field( $name, $post_id );
	return ( null === $value || '' === $value || false === $value ) ? $default : $value;
}

/**
 * Valeur d'un champ de la page « Infos du site ».
 *
 * @param string $name    Nom du champ (ex. 'phone').
 * @param mixed  $default Valeur si vide.
 * @return mixed
 */
function starter_option( string $name, mixed $default = '' ): mixed {
	if ( ! starter_scf_active() ) {
		return $default;
	}
	$value = get_field( $name, 'option' );
	return ( null === $value || '' === $value || false === $value ) ? $default : $value;
}
