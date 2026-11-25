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

/*
 * Conventions de nommage des champs : leurs choix sont remplis automatiquement.
 * - un champ Sélection nommé `icon`  → les icônes de assets/icons/ ;
 * - un champ Sélection nommé `color` → les couleurs de theme.json.
 * Ajouter une icône ou une couleur la rend disponible partout, sans toucher aux champs.
 */
add_filter( 'acf/load_field/name=icon', 'starter_scf_icon_choices' );
add_filter( 'acf/load_field/name=color', 'starter_scf_color_choices' );

/**
 * Choix d'un champ `icon` : un par fichier SVG.
 *
 * @param array<string, mixed> $field Réglages du champ.
 * @return array<string, mixed>
 */
function starter_scf_icon_choices( array $field ): array {
	if ( 'select' !== $field['type'] ) {
		return $field;
	}
	$field['choices'] = array();
	foreach ( starter_icon_list() as $name ) {
		$field['choices'][ $name ] = ucfirst( str_replace( '-', ' ', $name ) );
	}
	return $field;
}

/**
 * Choix d'un champ `color` : la palette de theme.json (slug => nom).
 *
 * @param array<string, mixed> $field Réglages du champ.
 * @return array<string, mixed>
 */
function starter_scf_color_choices( array $field ): array {
	if ( 'select' !== $field['type'] ) {
		return $field;
	}
	$field['choices'] = starter_palette();
	return $field;
}

/**
 * Palette de theme.json : [ slug => nom ].
 *
 * @return array<string, string>
 */
function starter_palette(): array {
	$palette = array();
	foreach ( wp_get_global_settings( array( 'color', 'palette', 'theme' ) ) as $color ) {
		$palette[ $color['slug'] ] = $color['name'];
	}
	return $palette;
}

/**
 * Palette de theme.json avec les valeurs : [ slug => '#rrggbb' ].
 *
 * @return array<string, string>
 */
function starter_palette_colors(): array {
	$colors = array();
	foreach ( wp_get_global_settings( array( 'color', 'palette', 'theme' ) ) as $color ) {
		$colors[ $color['slug'] ] = $color['color'];
	}
	return $colors;
}

/**
 * Une couleur de la palette est-elle claire ? (→ texte foncé par-dessus, sinon texte blanc)
 *
 * Calcul de la luminance relative (formule WCAG) à partir de la valeur hexadécimale
 * de theme.json : si le client change sa palette, le contraste suit tout seul.
 *
 * @param string $slug Slug de la couleur (ex. 'primary').
 */
function starter_color_is_light( string $slug ): bool {
	$hex = ltrim( starter_palette_colors()[ $slug ] ?? '#000000', '#' );
	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}

	$channel = static function ( string $pair ): float {
		$c = hexdec( $pair ) / 255;
		return $c <= 0.04045 ? $c / 12.92 : ( ( $c + 0.055 ) / 1.055 ) ** 2.4;
	};
	$luminance = 0.2126 * $channel( substr( $hex, 0, 2 ) )
		+ 0.7152 * $channel( substr( $hex, 2, 2 ) )
		+ 0.0722 * $channel( substr( $hex, 4, 2 ) );

	// Au-delà de ~0.18, un texte foncé contraste mieux qu'un texte blanc.
	return $luminance > 0.18;
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
