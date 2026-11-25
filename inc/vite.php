<?php
/**
 * Pont entre Vite et WordPress.
 *
 * Deux modes :
 * - DEV   : le fichier `hot` existe (créé par `npm run dev`) → on charge les
 *           fichiers sources depuis le serveur Vite (rechargement à chaud).
 * - PROD  : on lit dist/.vite/manifest.json pour trouver les fichiers compilés
 *           (dont le nom contient un hash, ex. main-a1b2c3.css).
 *
 * Voir docs/build.md.
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

/**
 * URL du serveur de dev Vite, ou null si on est en mode production.
 *
 * Par sécurité, le mode dev n'est possible qu'en environnement local
 * (WP_ENVIRONMENT_TYPE) : un fichier `hot` envoyé par erreur sur le serveur
 * ne cassera pas le site.
 */
function starter_vite_dev_url(): ?string {
	static $url = false;

	if ( false === $url ) {
		$hot = STARTER_DIR . '/hot';
		$dev = in_array( wp_get_environment_type(), array( 'local', 'development' ), true );
		$url = ( $dev && is_file( $hot ) ) ? rtrim( trim( (string) file_get_contents( $hot ) ), '/' ) : null;
	}

	return $url;
}

/**
 * Contenu du manifest Vite (tableau vide si `npm run build` n'a jamais été lancé).
 *
 * @return array<string, array<string, mixed>>
 */
function starter_vite_manifest(): array {
	static $manifest = null;

	if ( null === $manifest ) {
		$file     = STARTER_DIR . '/dist/.vite/manifest.json';
		$manifest = is_file( $file ) ? (array) json_decode( (string) file_get_contents( $file ), true ) : array();
	}

	return $manifest;
}

/**
 * Charge un point d'entrée Vite (fichier .css ou .js déclaré dans vite.config.js).
 *
 * @param string $handle Nom unique pour WordPress (ex. 'starter').
 * @param string $entry  Chemin source depuis la racine du thème (ex. 'assets/src/js/main.js').
 */
function starter_vite_enqueue( string $handle, string $entry ): void {
	$is_css = str_ends_with( $entry, '.css' );
	$dev    = starter_vite_dev_url();

	// --- Mode DEV : tout vient du serveur Vite. ---
	if ( $dev ) {
		// Client Vite : gère le rechargement à chaud. Chargé une seule fois.
		wp_enqueue_script_module( 'vite-client', $dev . '/@vite/client', array(), null );

		if ( $is_css ) {
			wp_enqueue_style( $handle, $dev . '/' . $entry, array(), null );
		} else {
			wp_enqueue_script_module( $handle, $dev . '/' . $entry, array(), null );
		}
		return;
	}

	// --- Mode PROD : fichiers compilés listés dans le manifest. ---
	$chunk = starter_vite_manifest()[ $entry ] ?? null;

	if ( ! $chunk ) {
		if ( current_user_can( 'manage_options' ) ) {
			trigger_error( esc_html( "Vite : « {$entry} » introuvable dans le manifest. Lancer `npm run build`." ), E_USER_WARNING );
		}
		return;
	}

	$dist = STARTER_URI . '/dist/';

	// Pas de numéro de version (null) : le hash dans le nom du fichier suffit
	// à forcer le navigateur à télécharger la nouvelle version.
	if ( $is_css ) {
		wp_enqueue_style( $handle, $dist . $chunk['file'], array(), null );
		return;
	}

	wp_enqueue_script_module( $handle, $dist . $chunk['file'], array(), null );

	// CSS importé depuis le JS (import './x.css') : Vite le sort dans un fichier à part.
	foreach ( $chunk['css'] ?? array() as $i => $css ) {
		wp_enqueue_style( "{$handle}-{$i}", $dist . $css, array(), null );
	}
}
