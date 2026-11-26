<?php
/**
 * SEO — fichiers lus par les robots :
 * - /wp-sitemap.xml : sitemap natif de WordPress, sans les pages « Ne pas indexer » ;
 * - /robots.txt     : généré (fichier virtuel), fermé à tous hors production ;
 * - /llms.txt       : résumé du site pour les IA (format llmstxt.org).
 *
 * Un VRAI fichier robots.txt ou llms.txt à la racine du site passe devant :
 * le supprimer pour utiliser ceux-ci.
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

/*
 * ---------------------------------------------------------------------------
 * Sitemap (natif WordPress, depuis 5.5)
 * ---------------------------------------------------------------------------
 */

add_filter( 'wp_sitemaps_posts_query_args', 'starter_seo_sitemap_exclude_noindex' );

/**
 * Retire du sitemap les contenus cochés « Ne pas indexer ».
 *
 * @param array<string, mixed> $args Arguments WP_Query du sitemap.
 * @return array<string, mixed>
 */
function starter_seo_sitemap_exclude_noindex( array $args ): array {
	if ( starter_seo_plugin_active() ) {
		return $args;
	}
	$args['meta_query']   = (array) ( $args['meta_query'] ?? array() ); // phpcs:ignore WordPress.DB.SlowDBQuery
	$args['meta_query'][] = array(
		'key'     => '_starter_seo_noindex',
		'compare' => 'NOT EXISTS',
	);
	return $args;
}

add_filter(
	'wp_sitemaps_taxonomies',
	static function ( array $taxonomies ): array {
		// Étiquettes : pages souvent pauvres en contenu, sans intérêt dans Google.
		unset( $taxonomies['post_tag'] );
		return $taxonomies;
	}
);

/*
 * ---------------------------------------------------------------------------
 * robots.txt
 * ---------------------------------------------------------------------------
 */

/**
 * Robots d'entraînement des IA (bloqués si la case est cochée dans Réglages > SEO).
 * Les robots des moteurs de recherche et des assistants qui citent leurs
 * sources (OAI-SearchBot, PerplexityBot…) ne sont PAS dans cette liste.
 *
 * @return string[]
 */
function starter_seo_ai_training_bots(): array {
	return (array) apply_filters(
		'starter_seo_ai_training_bots',
		array( 'GPTBot', 'Google-Extended', 'CCBot', 'ClaudeBot', 'anthropic-ai', 'Applebot-Extended', 'Bytespider', 'Meta-ExternalAgent', 'cohere-training-data-crawler' )
	);
}

add_filter( 'robots_txt', 'starter_seo_robots_txt', 20, 2 );

/**
 * Contenu du robots.txt.
 *
 * @param string $output Contenu généré par WordPress.
 * @param bool   $public Réglages > Lecture : site visible des moteurs ?
 */
function starter_seo_robots_txt( string $output, bool $public ): string {
	if ( starter_seo_plugin_active() ) {
		return $output;
	}

	// Préprod ou « ne pas indexer » coché : fermé à tous.
	if ( ! $public || ! starter_seo_site_indexable() ) {
		return "# Site non public (environnement : " . wp_get_environment_type() . ")\nUser-agent: *\nDisallow: /\n";
	}

	$path  = (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH );
	$lines = array(
		'User-agent: *',
		"Disallow: {$path}wp-admin/",
		"Allow: {$path}wp-admin/admin-ajax.php",
		"Disallow: {$path}?s=",
		"Disallow: {$path}search/",
	);

	// Seules les directives valides sont écrites (au cas où l'option aurait été remplie autrement).
	$extra = implode( "\n", preg_grep( '/^(User-agent|Disallow|Allow|Crawl-delay):\s*\S*/i', array_map( 'trim', explode( "\n", (string) starter_seo_setting( 'robots_extra' ) ) ) ) );
	if ( $extra ) {
		$lines[] = '';
		$lines[] = '# Réglages > SEO';
		$lines[] = $extra;
	}

	if ( starter_seo_setting( 'block_ai_training', 0 ) ) {
		$lines[] = '';
		$lines[] = '# Entraînement des IA : refusé (Réglages > SEO)';
		foreach ( starter_seo_ai_training_bots() as $bot ) {
			$lines[] = "User-agent: {$bot}";
		}
		$lines[] = 'Disallow: /';
	}

	$lines[] = '';
	$lines[] = 'Sitemap: ' . get_sitemap_url( 'index' );

	return implode( "\n", $lines ) . "\n";
}

/*
 * ---------------------------------------------------------------------------
 * llms.txt (https://llmstxt.org) : résumé Markdown du site pour les IA
 * ---------------------------------------------------------------------------
 */

const STARTER_SEO_LLMS_REWRITE = 'starter-llms-1'; // Changer la valeur force la régénération des permaliens.

add_action( 'init', 'starter_seo_llms_rewrite' );

/**
 * /llms.txt → index.php?starter_llms=1. Les permaliens sont régénérés une seule
 * fois (les mu-plugins n'ont pas de hook d'activation).
 */
function starter_seo_llms_rewrite(): void {
	add_rewrite_rule( '^llms\.txt$', 'index.php?starter_llms=1', 'top' );
	if ( get_option( 'starter_seo_rewrite' ) !== STARTER_SEO_LLMS_REWRITE ) {
		flush_rewrite_rules( false );
		update_option( 'starter_seo_rewrite', STARTER_SEO_LLMS_REWRITE );
	}
}

add_filter(
	'query_vars',
	static function ( array $vars ): array {
		$vars[] = 'starter_llms';
		return $vars;
	}
);

// Priorité 0 : avant redirect_canonical(), qui ajouterait un « / » final à l'adresse.
add_action( 'template_redirect', 'starter_seo_llms_output', 0 );

/**
 * Affiche /llms.txt.
 */
function starter_seo_llms_output(): void {
	if ( ! get_query_var( 'starter_llms' ) ) {
		return;
	}
	if ( starter_seo_plugin_active() || ! get_option( 'blog_public' ) ) {
		status_header( 404 );
		nocache_headers();
		exit;
	}

	header( 'Content-Type: text/plain; charset=utf-8' );
	header( 'X-Robots-Tag: noindex' ); // Le fichier lui-même n'a pas à apparaître dans Google.
	echo starter_seo_llms_content(); // phpcs:ignore WordPress.Security.EscapeOutput -- texte brut (Markdown), servi en text/plain.
	exit;
}

/**
 * Contenu Markdown de llms.txt. Mis en cache 12 h, vidé à chaque modification de contenu.
 */
function starter_seo_llms_content(): string {
	$cached = get_transient( 'starter_seo_llms' );
	if ( is_string( $cached ) ) {
		return $cached;
	}

	$clean = static fn( string $text ): string => trim( (string) preg_replace( '/\s+/u', ' ', wp_strip_all_tags( html_entity_decode( $text, ENT_QUOTES | ENT_HTML5, 'UTF-8' ) ) ) );
	$md    = array();

	// En-tête : nom, résumé, présentation.
	$md[] = '# ' . $clean( get_bloginfo( 'name' ) );
	$md[] = '';
	$front   = (int) get_option( 'page_on_front' );
	$summary = $front
		? ( starter_seo_meta( $front, 'description' ) ?: starter_seo_default_description( get_post( $front ) ) )
		: (string) get_bloginfo( 'description' );
	if ( $summary ) {
		$md[] = '> ' . $clean( $summary );
		$md[] = '';
	}
	$intro = trim( (string) starter_seo_setting( 'llms_intro' ) );
	if ( $intro ) {
		$md[] = $intro;
		$md[] = '';
	}

	// Coordonnées (« Infos du site »).
	$contact = array_filter(
		array(
			__( 'Téléphone', 'starter' ) => (string) starter_seo_site_info( 'phone' ),
			__( 'E-mail', 'starter' )    => (string) starter_seo_site_info( 'email' ),
			__( 'Adresse', 'starter' )   => implode( ', ', array_filter( array_map( 'trim', preg_split( '/\R|<br\s*\/?>/i', (string) starter_seo_site_info( 'address' ) ) ) ) ),
			__( 'Site', 'starter' )      => home_url( '/' ),
		)
	);
	foreach ( $contact as $label => $value ) {
		$md[] = "- {$label} : {$value}";
	}
	$md[] = '';

	// Une section par type de contenu public (pages d'abord), sans les « Ne pas indexer ».
	$types = starter_seo_post_types();
	usort( $types, static fn( $a, $b ): int => ( 'page' === $b ) <=> ( 'page' === $a ) );

	foreach ( $types as $type ) {
		$posts = get_posts(
			array(
				'post_type'      => $type,
				'post_status'    => 'publish',
				'posts_per_page' => 50,
				'orderby'        => 'page' === $type ? 'menu_order title' : 'date',
				'order'          => 'page' === $type ? 'ASC' : 'DESC',
				'has_password'   => false,
				'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery
					array(
						'key'     => '_starter_seo_noindex',
						'compare' => 'NOT EXISTS',
					),
				),
			)
		);
		if ( ! $posts ) {
			continue;
		}

		// Accueil en tête des pages.
		usort( $posts, static fn( $a, $b ): int => ( $b->ID === $front ) <=> ( $a->ID === $front ) );

		$md[] = '## ' . $clean( get_post_type_object( $type )->labels->name );
		$md[] = '';
		foreach ( $posts as $post ) {
			// Nom de la page (pas le titre SEO, souvent suffixé « | Marque »).
			$title = get_the_title( $post );
			$desc  = starter_seo_meta( $post->ID, 'description' ) ?: starter_seo_default_description( $post );
			$md[]  = sprintf( '- [%s](%s)%s', $clean( $title ), get_permalink( $post ), $desc ? ': ' . $clean( $desc ) : '' );
		}
		$md[] = '';
	}

	$content = implode( "\n", $md );
	/**
	 * Contenu de llms.txt avant envoi.
	 */
	$content = (string) apply_filters( 'starter_seo_llms', $content );
	set_transient( 'starter_seo_llms', $content, 12 * HOUR_IN_SECONDS );
	return $content;
}

// Cache vidé quand un contenu, les réglages SEO ou « Infos du site » changent.
foreach ( array( 'save_post', 'deleted_post', 'update_option_' . STARTER_SEO_OPTION, 'acf/save_post' ) as $starter_seo_hook ) {
	add_action( $starter_seo_hook, static fn() => delete_transient( 'starter_seo_llms' ) );
}
unset( $starter_seo_hook );
