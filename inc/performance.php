<?php
/**
 * Performance :
 * - préchargement de la police des titres (le grand titre est souvent l'élément
 *   le plus grand du 1er écran : LCP) ;
 * - images redimensionnées en WebP (≈ 30 % plus légères que le JPEG).
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

add_action( 'wp_head', 'starter_preload_fonts', 2 );

/**
 * <link rel="preload"> des polices utilisées dès le 1er écran.
 *
 * L'URL doit être EXACTEMENT celle du @font-face généré par WordPress depuis
 * theme.json (sinon la police est téléchargée deux fois) : même fonction,
 * get_theme_file_uri(). `crossorigin` est obligatoire pour les polices, même
 * servies par le site lui-même.
 */
function starter_preload_fonts(): void {
	$fonts = array( 'assets/fonts/onest.woff2' ); // Titres.

	foreach ( $fonts as $font ) {
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( get_theme_file_uri( $font ) )
		);
	}
}

add_filter( 'image_editor_output_format', 'starter_webp_output' );

/**
 * Tailles d'images générées (miniatures, medium, large…) en WebP au lieu du
 * JPEG / PNG. L'original téléversé n'est pas modifié.
 *
 * Seulement si le serveur sait produire du WebP (GD ou Imagick compilés avec).
 * Ne concerne que les images téléversées APRÈS l'activation du thème ; pour les
 * anciennes : `wp media regenerate`.
 *
 * @param array<string, string> $formats Format source => format de sortie.
 * @return array<string, string>
 */
function starter_webp_output( array $formats ): array {
	if ( wp_image_editor_supports( array( 'mime_type' => 'image/webp' ) ) ) {
		$formats['image/jpeg'] = 'image/webp';
		$formats['image/png']  = 'image/webp';
	}
	return $formats;
}
