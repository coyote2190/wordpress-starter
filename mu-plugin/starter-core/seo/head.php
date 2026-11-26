<?php
/**
 * SEO — ce qui est écrit dans le <head> :
 * - <title> (titre SEO de la page s'il est rempli) ;
 * - <meta name="description"> ;
 * - <meta name="robots" content="noindex"> pour les pages cochées « Ne pas indexer » (et tout le site en préprod) ;
 * - Open Graph + Twitter Card (aperçu des liens partagés) ;
 * - JSON-LD « LocalBusiness » sur l'accueil, depuis « Infos du site ».
 *
 * Tout le module SEO se désactive si une extension SEO est active (Yoast, Rank
 * Math, SEOPress, All in One SEO, The SEO Framework) : pas de balises en double.
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

/**
 * Une extension SEO est-elle active ? (Vérifié au moment des hooks : les
 * extensions sont chargées APRÈS les mu-plugins.)
 */
function starter_seo_plugin_active(): bool {
	return defined( 'WPSEO_VERSION' )
		|| defined( 'RANK_MATH_VERSION' )
		|| defined( 'SEOPRESS_VERSION' )
		|| defined( 'AIOSEO_VERSION' )
		|| defined( 'THE_SEO_FRAMEWORK_VERSION' );
}

/*
 * ---------------------------------------------------------------------------
 * Valeurs automatiques (quand les champs SEO sont vides)
 * ---------------------------------------------------------------------------
 */

/**
 * Données SCF du 1er bloc s'il s'agit d'un Hero ou d'un Hero slider du thème.
 *
 * @return array<string, mixed>
 */
function starter_seo_hero_data( WP_Post $post ): array {
	if ( ! has_blocks( $post ) ) {
		return array();
	}
	/**
	 * Blocs « en-tête » dont le texte et l'image servent de description / image par défaut.
	 */
	$heroes = (array) apply_filters( 'starter_seo_hero_blocks', array( 'starter/hero', 'starter/hero-slider' ) );
	foreach ( parse_blocks( $post->post_content ) as $block ) {
		if ( null === $block['blockName'] ) {
			continue; // Espaces entre les blocs.
		}
		return in_array( $block['blockName'], $heroes, true ) ? (array) ( $block['attrs']['data'] ?? array() ) : array();
	}
	return array();
}

/**
 * Texte brut d'un contenu Gutenberg (sans blocs dynamiques, balises ni espaces multiples).
 */
function starter_seo_plain( string $html ): string {
	// Fin de titre ou de paragraphe → « . » : « Données collectées. Le formulaire… » et non « …collectées Le formulaire… ».
	$html = (string) preg_replace( '#(?<![.!?:])\s*</(h[1-6]|li)>#i', '.</$1>', excerpt_remove_blocks( $html ) );
	$text = wp_strip_all_tags( strip_shortcodes( $html ) );
	return trim( (string) preg_replace( '/\s+/u', ' ', $text ) );
}

/**
 * Coupe à $max caractères, sur un mot, avec « … ».
 */
function starter_seo_trim( string $text, int $max = 160 ): string {
	$text = trim( (string) preg_replace( '/\s+/u', ' ', wp_strip_all_tags( $text ) ) );
	if ( mb_strlen( $text ) <= $max ) {
		return $text;
	}
	$cut = mb_substr( $text, 0, $max - 1 );
	$cut = preg_replace( '/\s+\S*$/u', '', $cut ) ?: $cut;
	return rtrim( $cut, ' ,.;:' ) . '…';
}

/**
 * Titre automatique d'un contenu, comme WordPress : « Titre – Nom du site ».
 */
function starter_seo_default_title( WP_Post $post ): string {
	$site = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
	if ( (int) get_option( 'page_on_front' ) === $post->ID ) {
		$tagline = wp_specialchars_decode( get_bloginfo( 'description' ), ENT_QUOTES );
		return $tagline ? "{$site} – {$tagline}" : $site;
	}
	return wp_specialchars_decode( get_the_title( $post ), ENT_QUOTES ) . " – {$site}";
}

/**
 * Description automatique d'un contenu : extrait → texte du Hero → début du contenu → slogan.
 */
function starter_seo_default_description( WP_Post $post ): string {
	$hero = starter_seo_hero_data( $post );
	$text = has_excerpt( $post ) ? $post->post_excerpt : (string) ( $hero['text'] ?? '' );
	$text = $text ?: starter_seo_plain( $post->post_content );
	return starter_seo_trim( $text ?: (string) get_bloginfo( 'description' ) );
}

/*
 * ---------------------------------------------------------------------------
 * Valeurs de la page affichée
 * ---------------------------------------------------------------------------
 */

/**
 * Contenu dont on lit les champs SEO : le contenu affiché, ou la page des
 * articles quand on est sur la liste des articles.
 */
function starter_seo_current_post(): ?WP_Post {
	if ( is_singular() ) {
		$post = get_queried_object();
		return $post instanceof WP_Post ? $post : null;
	}
	if ( is_home() && get_option( 'page_for_posts' ) ) {
		return get_post( (int) get_option( 'page_for_posts' ) );
	}
	return null;
}

/**
 * Description de la page affichée (160 caractères maximum).
 */
function starter_seo_description(): string {
	$post = starter_seo_current_post();
	if ( $post ) {
		return starter_seo_trim( starter_seo_meta( $post->ID, 'description' ) ?: starter_seo_default_description( $post ) );
	}
	if ( is_category() || is_tag() || is_tax() || is_post_type_archive() ) {
		$text = wp_strip_all_tags( get_the_archive_description() );
		if ( $text ) {
			return starter_seo_trim( $text );
		}
	}
	return starter_seo_trim( (string) get_bloginfo( 'description' ) );
}

/**
 * Image de partage : champ SEO → image mise en avant → Hero → image par défaut → icône du site.
 *
 * @return array{url: string, width: int, height: int, alt: string}|null
 */
function starter_seo_image(): ?array {
	$post = starter_seo_current_post();
	$id   = 0;

	if ( $post ) {
		$hero = starter_seo_hero_data( $post );
		$id   = (int) ( starter_seo_meta( $post->ID, 'image' ) ?: get_post_thumbnail_id( $post ) ?: ( $hero['image'] ?? $hero['slides_0_image'] ?? 0 ) );
	}
	$id = $id ?: (int) starter_seo_setting( 'default_image', 0 );

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

/*
 * ---------------------------------------------------------------------------
 * Sortie
 * ---------------------------------------------------------------------------
 */

add_filter( 'pre_get_document_title', 'starter_seo_document_title', 20 );

/**
 * <title> : titre SEO de la page s'il est rempli (utilisé tel quel).
 */
function starter_seo_document_title( string $title ): string {
	if ( starter_seo_plugin_active() || is_search() || is_404() ) {
		return $title;
	}
	$post = starter_seo_current_post();
	return ( $post ? starter_seo_meta( $post->ID, 'title' ) : '' ) ?: $title;
}

add_filter( 'wp_robots', 'starter_seo_robots' );

/**
 * <meta name="robots"> : noindex pour les pages cochées « Ne pas indexer ».
 *
 * @param array<string, bool|string> $robots Directives.
 * @return array<string, bool|string>
 */
function starter_seo_robots( array $robots ): array {
	$post = starter_seo_current_post();
	if ( ! starter_seo_plugin_active() && ( ! starter_seo_site_indexable() || ( $post && '1' === starter_seo_meta( $post->ID, 'noindex' ) ) ) ) {
		$robots['noindex'] = true;
		$robots['follow']  = true;
		unset( $robots['max-image-preview'] );
	}
	return $robots;
}

add_action( 'wp_head', 'starter_seo_meta_tags', 1 );

/**
 * Meta description + Open Graph + Twitter Card.
 */
function starter_seo_meta_tags(): void {
	if ( starter_seo_plugin_active() || is_404() || is_search() ) {
		return;
	}

	$description = starter_seo_description();
	$image       = starter_seo_image();
	$post        = starter_seo_current_post();
	$title       = ( $post ? starter_seo_meta( $post->ID, 'title' ) : '' )
		?: ( is_singular() && ! is_front_page() ? single_post_title( '', false ) : wp_specialchars_decode( wp_get_document_title(), ENT_QUOTES ) );

	$tags = array(
		array( 'name', 'description', $description ),
		array( 'property', 'og:type', is_singular( 'post' ) ? 'article' : 'website' ),
		array( 'property', 'og:locale', get_locale() ),
		array( 'property', 'og:site_name', wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) ),
		array( 'property', 'og:title', $title ),
		array( 'property', 'og:description', $description ),
		array( 'property', 'og:url', starter_seo_url() ),
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

/**
 * Adresse de « Infos du site » découpée pour schema.org : rue (une ou plusieurs
 * lignes) puis « code postal + ville » sur la dernière ligne.
 *
 * @return array<string, string>
 */
function starter_seo_postal_address(): array {
	$lines = array_values( array_filter( array_map( 'trim', preg_split( '/\R|<br\s*\/?>/i', (string) starter_seo_site_info( 'address' ) ) ) ) );
	if ( ! $lines ) {
		return array();
	}
	$address = array();
	if ( preg_match( '/^(\d{4,5})\s+(.+)$/u', (string) end( $lines ), $m ) ) {
		$address['postalCode']      = $m[1];
		$address['addressLocality'] = $m[2];
		array_pop( $lines );
	}
	if ( $lines ) {
		$address['streetAddress'] = implode( ', ', $lines );
	}
	return $address;
}

add_action( 'wp_head', 'starter_seo_schema', 2 );

/**
 * JSON-LD « LocalBusiness » sur l'accueil : nom, coordonnées, adresse, réseaux.
 *
 * Type modifiable (Restaurant, Store, ProfessionalService…) : filtre `starter_schema_type`.
 * Données modifiables (horaires, zone desservie…) : filtre `starter_schema`.
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

	$phone = (string) starter_seo_site_info( 'phone' );
	$email = (string) starter_seo_site_info( 'email' );
	if ( $phone ) {
		$schema['telephone'] = $phone;
	}
	if ( is_email( $email ) ) {
		$schema['email'] = $email;
	}

	$address = starter_seo_postal_address();
	if ( $address ) {
		$schema['address'] = array( '@type' => 'PostalAddress' ) + $address + array( 'addressCountry' => 'FR' );
	}

	$logo_id = (int) get_theme_mod( 'custom_logo' );
	$logo    = $logo_id ? wp_get_attachment_image_url( $logo_id, 'full' ) : get_site_icon_url( 512 );
	if ( $logo ) {
		$schema['logo']  = $logo;
		$schema['image'] = $logo;
	}

	$same_as = array();
	foreach ( (array) starter_seo_site_info( 'socials' ) as $social ) {
		if ( ! empty( $social['url'] ) ) {
			$same_as[] = esc_url_raw( $social['url'] );
		}
	}
	if ( $same_as ) {
		$schema['sameAs'] = $same_as;
	}

	/**
	 * Données structurées de l'accueil, avant affichage.
	 *
	 * @param array<string, mixed> $schema Données schema.org.
	 */
	$schema = (array) apply_filters( 'starter_schema', $schema );

	// JSON_HEX_TAG : un « </script> » dans une valeur ne peut pas fermer la balise.
	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG ) . '</script>' . "\n";
}
