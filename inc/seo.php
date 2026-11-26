<?php
/**
 * SEO de base, sans extension :
 * - <meta name="description"> ;
 * - Open Graph + Twitter Card (aperçu des liens sur LinkedIn, Facebook, WhatsApp, Slack…) ;
 * - données structurées JSON-LD « LocalBusiness » sur l'accueil, à partir de « Infos du site ».
 *
 * Désactivé automatiquement si une extension SEO est active (Yoast, Rank Math,
 * SEOPress, All in One SEO, The SEO Framework) : pas de balises en double.
 *
 * D'où vient la description (dans l'ordre) :
 * 1. l'extrait du contenu (champ « Extrait », aussi disponible sur les pages) ;
 * 2. le texte du Hero / Hero slider en tête de page ;
 * 3. le début du texte du contenu ;
 * 4. listes : description de la catégorie / du type de contenu, intro de la page des articles ;
 * 5. le slogan du site (Réglages > Général).
 *
 * Voir docs/seo.md.
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

/**
 * Une extension SEO gère-t-elle déjà les metas ?
 */
function starter_seo_plugin_active(): bool {
	return defined( 'WPSEO_VERSION' )
		|| defined( 'RANK_MATH_VERSION' )
		|| defined( 'SEOPRESS_VERSION' )
		|| defined( 'AIOSEO_VERSION' )
		|| defined( 'THE_SEO_FRAMEWORK_VERSION' );
}

/**
 * Données SCF du 1er bloc s'il s'agit d'un Hero ou d'un Hero slider.
 *
 * @return array<string, mixed>
 */
function starter_seo_hero_data( WP_Post $post ): array {
	if ( ! has_blocks( $post ) ) {
		return array();
	}
	foreach ( parse_blocks( $post->post_content ) as $block ) {
		if ( null === $block['blockName'] ) {
			continue; // Espaces entre les blocs.
		}
		return in_array( $block['blockName'], array( 'starter/hero', 'starter/hero-slider' ), true ) ? (array) ( $block['attrs']['data'] ?? array() ) : array();
	}
	return array();
}

/**
 * Texte brut d'un contenu Gutenberg (sans blocs dynamiques, balises ni espaces multiples).
 */
function starter_seo_plain( string $html ): string {
	$text = wp_strip_all_tags( strip_shortcodes( excerpt_remove_blocks( $html ) ) );
	return trim( (string) preg_replace( '/\s+/u', ' ', $text ) );
}

/**
 * Description de la page affichée (160 caractères maximum).
 */
function starter_seo_description(): string {
	$text = '';
	$post = get_queried_object();

	if ( is_singular() && $post instanceof WP_Post ) {
		$hero = starter_seo_hero_data( $post );
		$text = has_excerpt( $post ) ? $post->post_excerpt : (string) ( $hero['text'] ?? '' );
		$text = $text ?: starter_seo_plain( $post->post_content );
	} elseif ( is_home() && get_option( 'page_for_posts' ) ) {
		$text = starter_seo_plain( (string) get_post_field( 'post_content', (int) get_option( 'page_for_posts' ) ) );
	} elseif ( is_category() || is_tag() || is_tax() || is_post_type_archive() ) {
		$text = wp_strip_all_tags( get_the_archive_description() );
	}

	$text = trim( $text ) ?: (string) get_bloginfo( 'description' );
	$text = trim( (string) preg_replace( '/\s+/u', ' ', wp_strip_all_tags( $text ) ) );

	return mb_strlen( $text ) > 160 ? rtrim( mb_substr( $text, 0, 157 ), " ,.;:" ) . '…' : $text;
}

/**
 * Image de partage : image mise en avant, sinon image du Hero, sinon icône du site.
 *
 * @return array{url: string, width: int, height: int, alt: string}|null
 */
function starter_seo_image(): ?array {
	$post = get_queried_object();
	$id   = 0;

	if ( is_singular() && $post instanceof WP_Post ) {
		$hero = starter_seo_hero_data( $post );
		$id   = (int) ( get_post_thumbnail_id( $post ) ?: ( $hero['image'] ?? $hero['slides_0_image'] ?? 0 ) );
	}

	if ( $id ) {
		$src = wp_get_attachment_image_src( $id, 'large' );
		if ( $src ) {
			return array(
				'url'    => $src[0],
				'width'  => (int) $src[1],
				'height' => (int) $src[2],
				'alt'    => (string) get_post_meta( $id, '_wp_attachment_image_alt', true ),
			);
		}
	}

	$icon = get_site_icon_url( 512 );
	return $icon ? array(
		'url'    => $icon,
		'width'  => 512,
		'height' => 512,
		'alt'    => '',
	) : null;
}

/**
 * Adresse canonique de la page affichée (contenu, liste, catégorie).
 */
function starter_seo_url(): string {
	if ( is_singular() ) {
		return (string) get_permalink();
	}
	if ( is_home() && get_option( 'page_for_posts' ) ) {
		return (string) get_permalink( (int) get_option( 'page_for_posts' ) );
	}
	if ( is_category() || is_tag() || is_tax() ) {
		$link = get_term_link( get_queried_object() );
		return is_wp_error( $link ) ? home_url( '/' ) : $link;
	}
	if ( is_post_type_archive() ) {
		return (string) get_post_type_archive_link( (string) get_query_var( 'post_type' ) );
	}
	return home_url( '/' );
}

add_action( 'wp_head', 'starter_seo_meta', 1 );

/**
 * Meta description + Open Graph + Twitter Card.
 */
function starter_seo_meta(): void {
	if ( starter_seo_plugin_active() || is_404() || is_search() ) {
		return;
	}

	$description = starter_seo_description();
	$image       = starter_seo_image();
	$title       = is_singular() && ! is_front_page() ? single_post_title( '', false ) : wp_get_document_title();
	$url         = starter_seo_url();

	$tags = array(
		array( 'name', 'description', $description ),
		array( 'property', 'og:type', is_singular( 'post' ) ? 'article' : 'website' ),
		array( 'property', 'og:locale', get_locale() ),
		array( 'property', 'og:site_name', get_bloginfo( 'name' ) ),
		array( 'property', 'og:title', $title ),
		array( 'property', 'og:description', $description ),
		array( 'property', 'og:url', $url ),
		array( 'name', 'twitter:card', $image && $image['width'] > 512 ? 'summary_large_image' : 'summary' ),
	);
	if ( $image ) {
		$tags[] = array( 'property', 'og:image', $image['url'] );
		$tags[] = array( 'property', 'og:image:width', (string) $image['width'] );
		$tags[] = array( 'property', 'og:image:height', (string) $image['height'] );
		if ( $image['alt'] ) {
			$tags[] = array( 'property', 'og:image:alt', $image['alt'] );
		}
	}
	if ( is_singular( 'post' ) ) {
		$tags[] = array( 'property', 'article:published_time', (string) get_the_date( 'c' ) );
		$tags[] = array( 'property', 'article:modified_time', (string) get_the_modified_date( 'c' ) );
	}

	foreach ( $tags as [ $attr, $key, $value ] ) {
		if ( '' !== $value ) {
			printf( '<meta %s="%s" content="%s">' . "\n", esc_attr( $attr ), esc_attr( $key ), esc_attr( $value ) );
		}
	}
}

add_action( 'wp_head', 'starter_seo_schema', 2 );

/**
 * JSON-LD « LocalBusiness » sur l'accueil : nom, coordonnées, adresse, réseaux.
 * Aide Google à afficher la fiche de l'entreprise (adresse, téléphone…).
 *
 * Type modifiable (Restaurant, Store, ProfessionalService…) : filtre `starter_schema_type`.
 * Données modifiables : filtre `starter_schema`.
 */
function starter_seo_schema(): void {
	if ( starter_seo_plugin_active() || ! is_front_page() ) {
		return;
	}

	$schema = array(
		'@context'    => 'https://schema.org',
		'@type'       => (string) apply_filters( 'starter_schema_type', 'LocalBusiness' ),
		'@id'         => home_url( '/#organization' ),
		'name'        => wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ),
		'description' => starter_seo_description(),
		'url'         => home_url( '/' ),
	);

	$phone = (string) starter_option( 'phone' );
	$email = (string) starter_option( 'email' );
	if ( $phone ) {
		$schema['telephone'] = $phone;
	}
	if ( is_email( $email ) ) {
		$schema['email'] = $email;
	}

	// Adresse « rue (plusieurs lignes possibles) / code postal + ville » → PostalAddress.
	$lines = array_values( array_filter( array_map( 'trim', preg_split( '/\R|<br\s*\/?>/i', (string) starter_option( 'address' ) ) ) ) );
	if ( $lines ) {
		$address = array(
			'@type'          => 'PostalAddress',
			'addressCountry' => 'FR',
		);
		$last = (string) end( $lines );
		if ( preg_match( '/^(\d{4,5})\s+(.+)$/u', $last, $m ) ) {
			$address['postalCode']      = $m[1];
			$address['addressLocality'] = $m[2];
			array_pop( $lines );
		}
		if ( $lines ) {
			$address['streetAddress'] = implode( ', ', $lines );
		}
		$schema['address'] = $address;
	}

	$logo_id = (int) get_theme_mod( 'custom_logo' );
	$logo    = $logo_id ? wp_get_attachment_image_url( $logo_id, 'full' ) : get_site_icon_url( 512 );
	if ( $logo ) {
		$schema['logo']  = $logo;
		$schema['image'] = $logo;
	}

	$same_as = array();
	foreach ( (array) starter_option( 'socials', array() ) as $social ) {
		if ( ! empty( $social['url'] ) ) {
			$same_as[] = esc_url_raw( $social['url'] );
		}
	}
	if ( $same_as ) {
		$schema['sameAs'] = $same_as;
	}

	/**
	 * Données structurées de l'accueil, avant affichage (ajouter horaires, zone desservie…).
	 *
	 * @param array<string, mixed> $schema Données schema.org.
	 */
	$schema = (array) apply_filters( 'starter_schema', $schema );

	// JSON_HEX_TAG : un « </script> » dans une valeur ne peut pas fermer la balise.
	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG ) . '</script>' . "\n";
}
