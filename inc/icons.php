<?php
/**
 * Icônes SVG : insère le contenu d'un fichier assets/icons/{nom}.svg dans le HTML.
 *
 * Usage :
 *   <?php echo starter_icon( 'arrow-right', 'size-5' ); ?>
 *   <?php echo starter_icon( 'phone', 'size-4 text-primary', 'Téléphone' ); ?>
 *
 * Voir docs/icons.md.
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

/**
 * Retourne le SVG d'une icône, prêt à être affiché.
 *
 * @param string $name  Nom du fichier sans .svg (ex. 'arrow-right').
 * @param string $class Classes CSS ajoutées sur <svg> (ex. 'size-5 text-primary').
 * @param string $label Texte lu par les lecteurs d'écran. Vide = icône décorative (ignorée).
 * @return string HTML du SVG, ou chaîne vide si l'icône n'existe pas.
 */
function starter_icon( string $name, string $class = 'size-5', string $label = '' ): string {
	static $cache = array();

	// Le nom ne peut contenir que des lettres, chiffres et tirets : empêche
	// de lire un autre fichier du serveur (ex. '../../wp-config').
	if ( ! preg_match( '/^[a-z0-9-]+$/', $name ) ) {
		return '';
	}

	if ( ! isset( $cache[ $name ] ) ) {
		$file           = STARTER_DIR . '/assets/icons/' . $name . '.svg';
		$cache[ $name ] = is_file( $file ) ? trim( (string) file_get_contents( $file ) ) : '';
	}

	if ( '' === $cache[ $name ] ) {
		if ( WP_DEBUG ) {
			trigger_error( esc_html( "Icône « {$name} » introuvable dans assets/icons/." ), E_USER_NOTICE );
		}
		return '';
	}

	// Accessibilité : icône décorative → masquée ; sinon → annoncée avec son label.
	$a11y = '' === $label
		? 'aria-hidden="true" focusable="false"'
		: 'role="img" aria-label="' . esc_attr( $label ) . '"';

	$attributes = sprintf( 'class="%s" %s', esc_attr( trim( 'icon shrink-0 ' . $class ) ), $a11y );

	return preg_replace( '/^<svg\b/', '<svg ' . $attributes, $cache[ $name ], 1 );
}
