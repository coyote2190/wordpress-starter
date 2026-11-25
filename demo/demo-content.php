<?php
/**
 * Contenu de démo : remplit un site vide pour voir le thème en situation.
 *
 * Usage (depuis la racine WordPress) :
 *   wp eval-file wp-content/themes/wordpress-starter/demo/demo-content.php          → (re)crée la démo
 *   wp eval-file wp-content/themes/wordpress-starter/demo/demo-content.php reset    → supprime la démo
 *
 * Tout ce qui est créé est marqué (méta `_starter_demo`) : « reset » supprime
 * exactement ce contenu et remet les réglages modifiés comme avant.
 * Relancer le script supprime d'abord l'ancienne démo : on peut le lancer plusieurs fois.
 *
 * Voir docs/demo.md.
 *
 * @package Starter
 */

// Uniquement en ligne de commande (WP-CLI) : jamais accessible depuis le navigateur.
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	exit;
}

require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

const STARTER_DEMO_META  = '_starter_demo';
const STARTER_DEMO_STATE = 'starter_demo_state';

/*
 * ---------------------------------------------------------------------------
 * Suppression
 * ---------------------------------------------------------------------------
 */

/**
 * Supprime la démo et restaure les réglages d'origine.
 *
 * @param bool $keep_pages true = garde les pages (elles seront mises à jour par
 *                         starter_demo_create() : leurs ID et URL ne changent pas,
 *                         un éditeur ouvert sur une page de démo ne casse pas).
 */
function starter_demo_reset( bool $keep_pages = false ): void {
	$state = get_option( STARTER_DEMO_STATE, array() );

	// Contenus et médias marqués.
	$types = array_diff( get_post_types(), $keep_pages ? array( 'attachment', 'page' ) : array( 'attachment' ) );
	$ids   = get_posts(
		array(
			'post_type'      => array_values( $types ),
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'meta_key'       => STARTER_DEMO_META, // phpcs:ignore WordPress.DB.SlowDBQuery
		)
	);
	$attachments = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'meta_key'       => STARTER_DEMO_META, // phpcs:ignore WordPress.DB.SlowDBQuery
		)
	);
	foreach ( $ids as $id ) {
		wp_delete_post( $id, true );
	}
	foreach ( $attachments as $id ) {
		wp_delete_attachment( $id, true );
	}

	// Termes et menus créés par la démo.
	foreach ( $state['terms'] ?? array() as [ $term_id, $taxonomy ] ) {
		wp_delete_term( $term_id, $taxonomy );
	}
	foreach ( $state['menus'] ?? array() as $menu_id ) {
		wp_delete_nav_menu( $menu_id );
	}

	// Réglages remis comme avant la démo.
	foreach ( $state['options'] ?? array() as $name => $value ) {
		update_option( $name, $value );
	}
	if ( array_key_exists( 'menu_locations', $state ) ) {
		set_theme_mod( 'nav_menu_locations', $state['menu_locations'] );
	}
	if ( function_exists( 'delete_field' ) && ! empty( $state['site_settings'] ) ) {
		foreach ( array( 'phone', 'email', 'address', 'socials' ) as $field ) {
			delete_field( $field, 'option' );
		}
	}

	delete_option( STARTER_DEMO_STATE );

	WP_CLI::log( sprintf( 'Démo supprimée : %d contenus, %d images.', count( $ids ), count( $attachments ) ) );
}

/*
 * ---------------------------------------------------------------------------
 * Outils
 * ---------------------------------------------------------------------------
 */

/**
 * Marque un contenu / média comme « démo ».
 */
function starter_demo_mark( int $id ): int {
	update_post_meta( $id, STARTER_DEMO_META, 1 );
	return $id;
}

/**
 * Génère une image (dégradé aux couleurs du thème + titre) et l'ajoute à la médiathèque.
 *
 * @return int ID de l'image, 0 en cas d'échec.
 */
function starter_demo_image( string $title, string $from, string $to, bool $with_text = true, int $width = 1600, int $height = 900, string $label = '' ): int {
	if ( ! function_exists( 'imagecreatetruecolor' ) ) {
		return 0;
	}

	[ $w, $h ] = array( $width, $height );
	$img       = imagecreatetruecolor( $w, $h );
	$rgb       = static fn( string $hex ): array => sscanf( ltrim( $hex, '#' ), '%02x%02x%02x' );
	[ $r1, $g1, $b1 ] = $rgb( $from );
	[ $r2, $g2, $b2 ] = $rgb( $to );

	// Dégradé diagonal ligne par ligne.
	for ( $y = 0; $y < $h; $y++ ) {
		$t     = $y / $h;
		$color = imagecolorallocate( $img, (int) ( $r1 + ( $r2 - $r1 ) * $t ), (int) ( $g1 + ( $g2 - $g1 ) * $t ), (int) ( $b1 + ( $b2 - $b1 ) * $t ) );
		imageline( $img, 0, $y, $w, $y, $color );
	}

	// Titre centré si une police système est disponible (macOS / Linux).
	$fonts = array( '/System/Library/Fonts/Supplemental/Arial Bold.ttf', '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf' );
	$font  = current( array_filter( $fonts, 'is_file' ) );
	if ( $with_text && $font && function_exists( 'imagettftext' ) ) {
		$white = imagecolorallocatealpha( $img, 255, 255, 255, 30 );
		$text  = '' !== $label ? $label : $title;
		$box   = imagettfbbox( 64, 0, $font, $text );
		imagettftext( $img, 64, 0, (int) ( ( $w - ( $box[2] - $box[0] ) ) / 2 ), (int) ( $h / 2 + 32 ), $white, $font, $text );
	}

	$tmp = wp_tempnam( sanitize_title( $title ) . '.jpg' );
	imagejpeg( $img, $tmp, 82 );
	imagedestroy( $img );

	$id = media_handle_sideload(
		array(
			'name'     => 'demo-' . sanitize_title( $title ) . '.jpg',
			'tmp_name' => $tmp,
		),
		0,
		$title
	);

	if ( is_wp_error( $id ) ) {
		WP_CLI::warning( 'Image « ' . $title . ' » : ' . $id->get_error_message() );
		return 0;
	}

	update_post_meta( $id, '_wp_attachment_image_alt', $title );
	return starter_demo_mark( $id );
}

/**
 * Crée un contenu marqué « démo ».
 *
 * @param array<string, mixed> $args Arguments de wp_insert_post().
 */
function starter_demo_post( array $args, int $thumbnail = 0 ): int {
	// wp_insert_post() retire les antislashs (il attend des données « slashées »
	// comme celles d'un formulaire). Sans wp_slash(), le JSON des blocs perd ses
	// échappements : \u003cstrong\u003e deviendrait « u003cstrongu003e ».
	$id = wp_insert_post(
		wp_slash(
			array_merge(
				array(
					'post_status' => 'publish',
					'post_author' => 1,
				),
				$args
			)
		),
		true
	);

	if ( is_wp_error( $id ) ) {
		WP_CLI::error( $id->get_error_message() );
	}
	if ( $thumbnail ) {
		set_post_thumbnail( $id, $thumbnail );
	}
	return starter_demo_mark( $id );
}

/**
 * Crée un terme (ou réutilise l'existant) et retient ceux créés pour le reset.
 */
function starter_demo_term( string $name, string $taxonomy, array &$state ): int {
	$existing = term_exists( $name, $taxonomy );
	if ( $existing ) {
		return (int) $existing['term_id'];
	}
	$term             = wp_insert_term( $name, $taxonomy );
	$state['terms'][] = array( $term['term_id'], $taxonomy );
	return (int) $term['term_id'];
}

/*
 * Balisage des blocs. Format identique à ce que l'éditeur enregistre.
 */

function starter_demo_p( string $html ): string {
	return "<!-- wp:paragraph -->\n<p>{$html}</p>\n<!-- /wp:paragraph -->\n\n";
}

function starter_demo_h( string $text, int $level = 2 ): string {
	$attrs = 2 === $level ? '' : ' {"level":' . $level . '}';
	return "<!-- wp:heading{$attrs} -->\n<h{$level} class=\"wp-block-heading\">{$text}</h{$level}>\n<!-- /wp:heading -->\n\n";
}

/**
 * @param string[] $items
 */
function starter_demo_list( array $items ): string {
	$li = implode( "\n", array_map( static fn( $i ) => "<!-- wp:list-item -->\n<li>{$i}</li>\n<!-- /wp:list-item -->", $items ) );
	return "<!-- wp:list -->\n<ul class=\"wp-block-list\">{$li}</ul>\n<!-- /wp:list -->\n\n";
}

function starter_demo_button( string $label, string $url ): string {
	return "<!-- wp:buttons -->\n<div class=\"wp-block-buttons\"><!-- wp:button -->\n<div class=\"wp-block-button\"><a class=\"wp-block-button__link wp-element-button\" href=\"" . esc_url( $url ) . "\">{$label}</a></div>\n<!-- /wp:button --></div>\n<!-- /wp:buttons -->\n\n";
}

/**
 * Bloc auto-fermant (blocs SCF et natifs dynamiques) : <!-- wp:nom {attributs} /-->.
 *
 * @param array<string, mixed> $attrs
 */
function starter_demo_block( string $name, array $attrs = array() ): string {
	return serialize_block(
		array(
			'blockName'    => $name,
			'attrs'        => $attrs,
			'innerBlocks'  => array(),
			'innerHTML'    => '',
			'innerContent' => array(),
		)
	) . "\n\n";
}

/**
 * Bloc SCF Hero. Les données SCF d'un bloc : { champ: valeur, _champ: clé_du_champ }.
 *
 * @param array{title: string, url: string}|null $button
 */
function starter_demo_hero( string $title, string $text, ?array $button, int $image ): string {
	return starter_demo_block(
		'starter/hero',
		array(
			'name'  => 'starter/hero',
			'mode'  => 'preview',
			'align' => 'full',
			'data'  => array(
				'title'   => $title,
				'_title'  => 'field_starter_hero_title',
				'text'    => $text,
				'_text'   => 'field_starter_hero_text',
				'button'  => $button ? $button + array( 'target' => '' ) : '',
				'_button' => 'field_starter_hero_button',
				'image'   => $image ?: '',
				'_image'  => 'field_starter_hero_image',
			),
		)
	);
}

/**
 * Bloc SCF Cartes. Dans un bloc, un répéteur est stocké « à plat », comme en base :
 * items = nombre de lignes, items_0_title = valeur, _items_0_title = clé du champ.
 *
 * @param array<int, array{icon: string, color: string, title: string, text: string, link?: array{title: string, url: string}}> $items
 */
function starter_demo_features( string $title, string $intro, string $columns, string $style, array $items ): string {
	$k    = 'field_starter_features_';
	$data = array(
		'title'    => $title,
		'_title'   => $k . 'title',
		'intro'    => $intro,
		'_intro'   => $k . 'intro',
		'columns'  => $columns,
		'_columns' => $k . 'columns',
		'style'    => $style,
		'_style'   => $k . 'style',
		'items'    => count( $items ),
		'_items'   => $k . 'items',
	);
	foreach ( $items as $i => $item ) {
		$item += array( 'link' => '' );
		if ( is_array( $item['link'] ) ) {
			$item['link'] += array( 'target' => '' );
		}
		foreach ( array( 'icon', 'color', 'title', 'text', 'link' ) as $sub ) {
			$data[ "items_{$i}_{$sub}" ]  = $item[ $sub ];
			$data[ "_items_{$i}_{$sub}" ] = $k . 'item_' . $sub;
		}
	}

	return starter_demo_block(
		'starter/features',
		array(
			'name'  => 'starter/features',
			'mode'  => 'preview',
			'align' => 'wide',
			'data'  => $data,
		)
	);
}

/**
 * Bloc SCF Hero slider (répéteur « slides » stocké à plat, comme pour les Cartes).
 *
 * @param array<int, array{image: int, title: string, title_highlight?: string, text?: string, button_primary?: array, button_secondary?: array}> $slides
 */
function starter_demo_hero_slider( array $slides ): string {
	$k    = 'field_starter_slider_';
	$data = array(
		'slides'         => count( $slides ),
		'_slides'        => $k . 'slides',
		'height'         => 'screen',
		'_height'        => $k . 'height',
		'content_align'  => 'left',
		'_content_align' => $k . 'content_align',
		'overlay'        => 'medium',
		'_overlay'       => $k . 'overlay',
		'autoplay'       => 6,
		'_autoplay'      => $k . 'autoplay',
	);
	foreach ( $slides as $i => $slide ) {
		foreach ( array( 'image', 'title', 'title_highlight', 'text', 'button_primary', 'button_secondary' ) as $sub ) {
			$value = $slide[ $sub ] ?? '';
			if ( is_array( $value ) ) {
				$value += array( 'target' => '' );
			}
			$data[ "slides_{$i}_{$sub}" ]  = $value;
			$data[ "_slides_{$i}_{$sub}" ] = $k . 'slide_' . $sub;
		}
	}

	return starter_demo_block(
		'starter/hero-slider',
		array(
			'name'  => 'starter/hero-slider',
			'mode'  => 'preview',
			'align' => 'full',
			'data'  => $data,
		)
	);
}

/**
 * Bloc SCF Bento (répéteur « tiles » stocké à plat).
 *
 * @param array<int, array<string, mixed>> $tiles
 */
function starter_demo_bento( string $title, string $intro, array $tiles ): string {
	$k    = 'field_starter_bento_';
	$data = array(
		'title'  => $title,
		'_title' => $k . 'title',
		'intro'  => $intro,
		'_intro' => $k . 'intro',
		'tiles'  => count( $tiles ),
		'_tiles' => $k . 'tiles',
	);
	foreach ( $tiles as $i => $tile ) {
		$tile += array(
			'type'   => 'color',
			'width'  => '1',
			'height' => 'normal',
			'image'  => '',
			'color'  => 'primary',
			'badge'  => '',
			'number' => '',
			'title'  => '',
			'text'   => '',
			'link'   => '',
		);
		foreach ( $tile as $sub => $value ) {
			if ( is_array( $value ) ) {
				$value += array( 'target' => '' );
			}
			$data[ "tiles_{$i}_{$sub}" ]  = $value;
			$data[ "_tiles_{$i}_{$sub}" ] = $k . 'tile_' . $sub;
		}
	}

	return starter_demo_block(
		'starter/bento',
		array(
			'name'  => 'starter/bento',
			'mode'  => 'preview',
			'align' => 'wide',
			'data'  => $data,
		)
	);
}

/**
 * Bloc SCF Appel à l'action.
 *
 * @param array<string, mixed>                          $args   title, text, action, button, background, accent.
 * @param array<int, array{image: int, name: string}> $people Personnes autour du titre.
 */
function starter_demo_cta( array $args, array $people = array() ): string {
	$k    = 'field_starter_cta_';
	$data = array();
	foreach ( $args as $name => $value ) {
		$data[ $name ]       = is_array( $value ) ? $value + array( 'target' => '' ) : $value;
		$data[ '_' . $name ] = $k . $name;
	}
	$data['people']  = count( $people );
	$data['_people'] = $k . 'people';
	foreach ( $people as $i => $person ) {
		foreach ( array( 'image', 'name' ) as $sub ) {
			$data[ "people_{$i}_{$sub}" ]  = $person[ $sub ];
			$data[ "_people_{$i}_{$sub}" ] = $k . 'person_' . $sub;
		}
	}

	return starter_demo_block(
		'starter/cta',
		array(
			'name'  => 'starter/cta',
			'mode'  => 'preview',
			'align' => 'wide',
			'data'  => $data,
		)
	);
}

/**
 * Bloc SCF Pourquoi nous (intro + cartes défilantes).
 *
 * @param array<string, mixed>             $args  title, text, action, button.
 * @param array<int, array<string, mixed>> $cards badge, color, title, text, icon, link.
 */
function starter_demo_why( array $args, array $cards ): string {
	$k    = 'field_starter_why_';
	$data = array();
	foreach ( $args as $name => $value ) {
		$data[ $name ]       = is_array( $value ) ? $value + array( 'target' => '' ) : $value;
		$data[ '_' . $name ] = $k . $name;
	}
	$data['cards']  = count( $cards );
	$data['_cards'] = $k . 'cards';
	foreach ( $cards as $i => $card ) {
		$card += array( 'badge' => '', 'color' => 'primary', 'title' => '', 'text' => '', 'icon' => '', 'image' => '', 'link' => '' );
		foreach ( $card as $sub => $value ) {
			$data[ "cards_{$i}_{$sub}" ]  = is_array( $value ) ? $value + array( 'target' => '' ) : $value;
			$data[ "_cards_{$i}_{$sub}" ] = $k . 'card_' . $sub;
		}
	}

	return starter_demo_block(
		'starter/why',
		array(
			'name'  => 'starter/why',
			'mode'  => 'preview',
			'align' => 'full',
			'data'  => $data,
		)
	);
}

function starter_demo_notice( string $type, string $content ): string {
	return starter_demo_block(
		'starter/notice',
		array(
			'type'    => $type,
			'content' => $content,
		)
	);
}

/*
 * ---------------------------------------------------------------------------
 * Création
 * ---------------------------------------------------------------------------
 */

/**
 * Crée toute la démo.
 */
function starter_demo_create(): void {
	$state = array(
		'terms'   => array(),
		'menus'   => array(),
		// Réglages modifiés par la démo : valeur d'origine, restaurée par « reset ».
		'options' => array(),
	);
	foreach ( array( 'show_on_front', 'page_on_front', 'page_for_posts', 'blogdescription', 'wp_page_for_privacy_policy', 'posts_per_page' ) as $option ) {
		$state['options'][ $option ] = get_option( $option );
	}
	$state['menu_locations'] = get_theme_mod( 'nav_menu_locations', array() );

	// Couleurs du thème (theme.json) pour les images.
	$palette = array();
	foreach ( wp_get_global_settings( array( 'color', 'palette', 'theme' ) ) as $color ) {
		$palette[ $color['slug'] ] = $color['color'];
	}
	$primary   = $palette['primary'] ?? '#0f4c81';
	$secondary = $palette['secondary'] ?? '#f2a541';
	$dark      = $palette['dark'] ?? '#111827';

	// --- Images -----------------------------------------------------------
	WP_CLI::log( 'Images…' );
	$img = array(
		'slide1'  => starter_demo_image( 'Diapositive 1', $dark, $primary, false ),
		'slide2'  => starter_demo_image( 'Diapositive 2', $primary, $secondary, false ),
		'slide3'  => starter_demo_image( 'Diapositive 3', $secondary, $dark, false ),
		'bento1'  => starter_demo_image( 'Bento sites vitrines', $secondary, $primary, false ),
		'bento2'  => starter_demo_image( 'Bento identité', $primary, $dark, false ),
		'avatar1' => starter_demo_image( 'Avatar Léa', $secondary, $primary, true, 400, 400, 'LM' ),
		'avatar2' => starter_demo_image( 'Avatar Hugo', $primary, $dark, true, 400, 400, 'HB' ),
		'avatar3' => starter_demo_image( 'Avatar Inès', $dark, $secondary, true, 400, 400, 'IK' ),
		'avatar4' => starter_demo_image( 'Avatar Tom', $secondary, $dark, true, 400, 400, 'TR' ),
		'about'   => starter_demo_image( 'Notre équipe', $primary, $dark ),
		'contact' => starter_demo_image( 'Parlons de votre projet', $primary, $secondary ),
	);
	$colors = array( array( $primary, $dark ), array( $secondary, $primary ), array( $dark, $secondary ) );

	// --- Infos du site (SCF) ---------------------------------------------
	if ( function_exists( 'update_field' ) ) {
		update_field( 'phone', '04 91 00 00 00', 'option' );
		update_field( 'email', get_option( 'admin_email' ), 'option' );
		update_field( 'address', "12 quai du Port\n13002 Marseille", 'option' );
		update_field(
			'socials',
			array(
				array( 'network' => 'instagram', 'url' => 'https://www.instagram.com/' ),
				array( 'network' => 'linkedin', 'url' => 'https://www.linkedin.com/' ),
			),
			'option'
		);
		$state['site_settings'] = true;
	} else {
		WP_CLI::warning( 'SCF inactif : « Infos du site » non rempli.' );
	}

	// --- Articles ---------------------------------------------------------
	WP_CLI::log( 'Articles…' );
	$cat_tips = starter_demo_term( 'Conseils', 'category', $state );
	$cat_news = starter_demo_term( 'Vie du studio', 'category', $state );

	$articles = array(
		array( '5 erreurs à éviter sur la page d\'accueil', $cat_tips, 'Une page d\'accueil doit dire en trois secondes qui vous êtes, ce que vous proposez et quoi faire ensuite.' ),
		array( 'Pourquoi votre site doit être rapide', $cat_tips, 'Chaque seconde de chargement en plus fait fuir des visiteurs. Voici les leviers simples pour accélérer un site.' ),
		array( 'Nous emménageons sur le Vieux-Port', $cat_news, 'Après trois ans en coworking, le studio s\'installe dans ses propres locaux, face à la mer.' ),
		array( 'RGPD : le formulaire de contact en 4 points', $cat_tips, 'Consentement, durée de conservation, information : ce qu\'un formulaire de contact doit respecter.' ),
		array( 'Bienvenue à Léa, notre nouvelle développeuse', $cat_news, 'Léa rejoint l\'équipe pour renforcer le pôle développement WordPress.' ),
	);
	foreach ( $articles as $i => [ $title, $cat, $excerpt ] ) {
		[ $from, $to ] = $colors[ $i % 3 ];
		$content       = starter_demo_p( $excerpt )
			. starter_demo_h( 'Le constat' )
			. starter_demo_p( 'Lorem ipsum dolor sit amet, <strong>consectetur adipiscing elit</strong>. Integer nec odio. Praesent libero. Sed cursus ante dapibus diam. Sed nisi. Nulla quis sem at nibh elementum imperdiet.' )
			. starter_demo_list( array( 'Un message clair dès le premier écran', 'Un seul appel à l\'action principal', 'Des preuves : avis, chiffres, références' ) )
			. starter_demo_notice( 'info', 'Besoin d\'un regard extérieur ? <a href="/contact/">Demandez un audit gratuit</a>.' )
			. starter_demo_h( 'Ce qu\'on recommande' )
			. starter_demo_p( 'Duis sagittis ipsum. Praesent mauris. Fusce nec tellus sed augue semper porta. Mauris massa. Vestibulum lacinia arcu eget nulla. Class aptent taciti sociosqu ad litora torquent per conubia nostra.' );

		starter_demo_post(
			array(
				'post_type'     => 'post',
				'post_title'    => $title,
				'post_excerpt'  => $excerpt,
				'post_content'  => $content,
				'post_category' => array( $cat ),
				'post_date'     => wp_date( 'Y-m-d H:i:s', strtotime( '-' . ( $i * 9 + 2 ) . ' days' ) ),
			),
			starter_demo_image( $title, $from, $to )
		);
	}

	// --- Réalisations (CPT du mu-plugin) ---------------------------------
	if ( post_type_exists( 'reference' ) ) {
		WP_CLI::log( 'Réalisations…' );
		$ref_cats = array(
			'vitrine' => starter_demo_term( 'Site vitrine', 'reference_category', $state ),
			'shop'    => starter_demo_term( 'E-commerce', 'reference_category', $state ),
			'brand'   => starter_demo_term( 'Identité visuelle', 'reference_category', $state ),
		);
		$references = array(
			array( 'Boulangerie Lou Pan', 'vitrine', 'Site vitrine avec carte des produits et horaires mis à jour par l\'équipe.' ),
			array( 'Calanques Kayak', 'shop', 'Réservation de sorties en ligne et paiement sécurisé.' ),
			array( 'Cabinet Martin Avocats', 'vitrine', 'Refonte complète, prise de rendez-vous et articles juridiques.' ),
			array( 'Savonnerie du Midi', 'shop', 'Boutique en ligne de 80 produits, livraison et retrait en atelier.' ),
			array( 'Festival Mar & Sons', 'brand', 'Logo, charte graphique et site de l\'événement.' ),
			array( 'Atelier Céramique Brun', 'brand', 'Identité visuelle et portfolio des créations.' ),
		);
		foreach ( $references as $i => [ $title, $cat, $excerpt ] ) {
			[ $from, $to ] = $colors[ ( $i + 1 ) % 3 ];
			$id            = starter_demo_post(
				array(
					'post_type'    => 'reference',
					'post_title'   => $title,
					'post_excerpt' => $excerpt,
					'post_content' => starter_demo_p( $excerpt )
						. starter_demo_h( 'Le besoin' )
						. starter_demo_p( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer nec odio. Praesent libero. Sed cursus ante dapibus diam.' )
						. starter_demo_h( 'Ce que nous avons fait' )
						. starter_demo_list( array( 'Ateliers de cadrage', 'Maquettes et prototype', 'Développement WordPress sur mesure', 'Formation de l\'équipe' ) )
						. starter_demo_notice( 'success', '<strong>Résultat :</strong> +40 % de demandes de contact en 6 mois.' ),
					'post_date'    => wp_date( 'Y-m-d H:i:s', strtotime( '-' . ( $i * 20 + 5 ) . ' days' ) ),
				),
				starter_demo_image( $title, $from, $to )
			);
			wp_set_object_terms( $id, array( $ref_cats[ $cat ] ), 'reference_category' );
		}
	} else {
		WP_CLI::warning( 'CPT « reference » absent (mu-plugin non installé) : pas de réalisations.' );
	}

	// --- Pages ------------------------------------------------------------
	WP_CLI::log( 'Pages…' );
	// Page existante (démo précédente, même slug) → mise à jour : même ID, même URL.
	$page = static function ( string $title, string $content, string $slug = '' ): int {
		$slug     = $slug ?: sanitize_title( $title );
		$existing = get_posts(
			array(
				'post_type'      => 'page',
				'name'           => $slug,
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => STARTER_DEMO_META, // phpcs:ignore WordPress.DB.SlowDBQuery
			)
		);

		return starter_demo_post(
			array(
				'ID'           => $existing[0] ?? 0,
				'post_type'    => 'page',
				'post_title'   => $title,
				'post_name'    => $slug,
				'post_content' => $content,
			)
		);
	};

	$home = $page(
		'Accueil',
		starter_demo_hero_slider(
			array(
				array(
					'image'            => $img['slide1'],
					'title'            => 'Des sites web',
					'title_highlight'  => 'qui travaillent pour vous',
					'text'             => 'Studio Horizon conçoit des sites rapides, accessibles et faciles à mettre à jour pour les entreprises de la région.',
					'button_primary'   => array( 'title' => 'Démarrer un projet', 'url' => '/contact/' ),
					'button_secondary' => array( 'title' => 'Nos réalisations', 'url' => '/realisations/' ),
				),
				array(
					'image'            => $img['slide2'],
					'title'            => 'Vendez en ligne',
					'title_highlight'  => 'simplement',
					'text'             => 'Une boutique facile à gérer, avec paiement sécurisé et livraison intégrée.',
					'button_primary'   => array( 'title' => 'Voir nos boutiques', 'url' => '/realisations/categorie/e-commerce/' ),
				),
				array(
					'image'            => $img['slide3'],
					'title'            => 'Une identité',
					'title_highlight'  => 'qui vous ressemble',
					'text'             => 'Logo, couleurs, typographies : une image cohérente sur tous vos supports.',
					'button_primary'   => array( 'title' => 'Parlons-en', 'url' => '/contact/' ),
					'button_secondary' => array( 'title' => 'À propos', 'url' => '/a-propos/' ),
				),
			)
		)
		. starter_demo_bento(
			'Ce que nous faisons',
			'De la première maquette à la mise en ligne, nous vous accompagnons à chaque étape.',
			array(
				array( 'type' => 'image', 'image' => $img['bento1'], 'color' => 'primary', 'width' => '2', 'height' => 'tall', 'badge' => 'Service', 'title' => 'Sites vitrines', 'text' => 'Un site clair qui présente votre activité et transforme les visiteurs en clients.', 'link' => array( 'title' => 'Voir les sites vitrines', 'url' => '/realisations/categorie/site-vitrine/' ) ),
				array( 'color' => 'light', 'number' => '120 +', 'text' => 'sites et boutiques livrés depuis 2019, de Marseille à Lyon.', 'link' => array( 'title' => 'Toutes nos réalisations', 'url' => '/realisations/' ) ),
				array( 'color' => 'secondary', 'badge' => 'Service', 'title' => 'E-commerce', 'text' => 'Une boutique simple à gérer, paiement et livraison intégrés.', 'link' => array( 'title' => 'Voir les boutiques', 'url' => '/realisations/categorie/e-commerce/' ) ),
				array( 'type' => 'image', 'image' => $img['bento2'], 'color' => 'dark', 'width' => '2', 'badge' => 'Service', 'title' => 'Identité visuelle', 'text' => 'Logo, couleurs, typographies : une image cohérente sur tous vos supports.', 'link' => array( 'title' => 'Voir les identités visuelles', 'url' => '/realisations/categorie/identite-visuelle/' ) ),
			)
		)
		. starter_demo_notice( 'info', 'Premier rendez-vous <strong>offert</strong> : on fait le point sur votre projet en 30 minutes.' )
		. starter_demo_why(
			array(
				'title'  => "Rapides.\nSimples.\n**Sereins.**",
				'text'   => 'Pourquoi nous choisir ? Parce qu\'un bon site se mesure à ce qu\'il vous fait gagner : du temps, des clients et de la tranquillité.',
				'action' => 'button',
				'button' => array( 'title' => 'Demander un devis', 'url' => '/contact/' ),
			),
			array(
				array( 'badge' => 'Performance', 'color' => 'primary', 'icon' => 'rocket', 'title' => 'Des sites rapides', 'text' => 'Des pages légères, bien notées par Google et agréables sur mobile.' ),
				array( 'badge' => 'Autonomie', 'color' => 'secondary', 'icon' => 'settings', 'title' => 'Faciles à gérer', 'text' => 'Vous modifiez vos textes et photos vous-même, sans risque de tout casser.' ),
				array( 'badge' => 'Sérénité', 'color' => 'dark', 'icon' => 'shield', 'title' => 'Sécurisés et suivis', 'text' => 'Mises à jour, sauvegardes et surveillance : on s\'en occupe.' ),
				array( 'badge' => 'Proximité', 'color' => 'primary', 'icon' => 'users', 'title' => 'Un seul contact', 'text' => 'Du devis au suivi, la même personne, joignable par téléphone.', 'link' => array( 'title' => 'Découvrir l\'équipe', 'url' => '/a-propos/' ) ),
				array( 'badge' => 'Résultats', 'color' => 'secondary', 'icon' => 'chart', 'title' => 'Des clients en plus', 'text' => 'Un site pensé pour transformer les visiteurs en demandes de contact.', 'link' => array( 'title' => 'Voir nos réalisations', 'url' => '/realisations/' ) ),
			)
		)
		. starter_demo_cta(
			array(
				'title'       => 'Lancez **votre projet** web __dès aujourd\'hui__',
				'text'        => 'Laissez-nous votre e-mail : on vous rappelle sous 48 h pour en parler, sans engagement.',
				'action'      => 'email',
				'button'      => array( 'title' => 'C\'est parti', 'url' => '/contact/' ),
				'placeholder' => 'votre@email.fr',
				'background'  => 'light',
				'accent'      => 'secondary',
			),
			array(
				array( 'image' => $img['avatar1'], 'name' => 'Léa' ),
				array( 'image' => $img['avatar2'], 'name' => 'Hugo' ),
				array( 'image' => $img['avatar3'], 'name' => 'Inès' ),
				array( 'image' => $img['avatar4'], 'name' => 'Tom' ),
			)
		),
		'accueil'
	);

	$about = $page(
		'À propos',
		starter_demo_hero( 'Une équipe à taille humaine', 'Quatre passionnés du web installés à Marseille depuis 2019.', null, $img['about'] )
		. starter_demo_h( 'Notre histoire' )
		. starter_demo_p( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer nec odio. Praesent libero. Sed cursus ante dapibus diam. Sed nisi. Nulla quis sem at nibh elementum imperdiet. Duis sagittis ipsum.' )
		. starter_demo_features(
			'Nos valeurs',
			'',
			'3',
			'light',
			array(
				array( 'icon' => 'lightbulb', 'color' => 'secondary', 'title' => 'Clarté', 'text' => 'Des devis lisibles, des délais tenus, pas de jargon.' ),
				array( 'icon' => 'star', 'color' => 'primary', 'title' => 'Exigence', 'text' => 'Performance, accessibilité et sécurité dès la conception.' ),
				array( 'icon' => 'heart', 'color' => 'secondary', 'title' => 'Proximité', 'text' => 'Nous vous rencontrons, et nous répondons au téléphone.' ),
			)
		)
		. starter_demo_notice( 'warning', 'Le studio est fermé du 24 décembre au 2 janvier.' ),
		'a-propos'
	);

	$contact = $page(
		'Contact',
		starter_demo_hero( 'Parlons de votre projet', 'Réponse sous 48 h ouvrées.', null, $img['contact'] )
		. starter_demo_p( 'Un projet, une question, un devis ? Remplissez le formulaire ou appelez-nous au <a href="tel:0491000000">04 91 00 00 00</a>.' )
		. starter_demo_block( 'starter/contact-form', array( 'name' => 'starter/contact-form', 'mode' => 'preview' ) ),
		'contact'
	);

	$blog    = $page( 'Actualités', '', 'actualites' );
	$legal   = $page(
		'Mentions légales',
		starter_demo_h( 'Éditeur du site' )
		. starter_demo_p( 'Studio Horizon — SARL au capital de 10 000 € — RCS Marseille 000 000 000<br>12 quai du Port, 13002 Marseille' )
		. starter_demo_h( 'Hébergement' )
		. starter_demo_p( 'Nom de l\'hébergeur, adresse, téléphone.' ),
		'mentions-legales'
	);
	$privacy = $page(
		'Politique de confidentialité',
		starter_demo_h( 'Données collectées' )
		. starter_demo_p( 'Le formulaire de contact collecte votre nom, votre e-mail, votre téléphone (facultatif) et votre message, uniquement pour vous répondre.' )
		. starter_demo_h( 'Durée de conservation' )
		. starter_demo_p( 'Les messages sont conservés 3 ans maximum dans notre messagerie.' )
		. starter_demo_h( 'Vos droits' )
		. starter_demo_p( 'Vous pouvez demander l\'accès, la rectification ou la suppression de vos données en nous écrivant.' ),
		'politique-de-confidentialite'
	);

	// --- Réglages de lecture ---------------------------------------------
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $home );
	update_option( 'page_for_posts', $blog );
	update_option( 'posts_per_page', 6 );
	update_option( 'wp_page_for_privacy_policy', $privacy );
	update_option( 'blogdescription', 'Studio web à Marseille' );

	// --- Menus ------------------------------------------------------------
	WP_CLI::log( 'Menus…' );
	$add_page = static fn( int $menu, int $page_id ) => wp_update_nav_menu_item(
		$menu,
		0,
		array(
			'menu-item-object-id' => $page_id,
			'menu-item-object'    => 'page',
			'menu-item-type'      => 'post_type',
			'menu-item-status'    => 'publish',
		)
	);

	$primary_menu = wp_create_nav_menu( 'Démo – Principal' );
	$add_page( $primary_menu, $home );
	$add_page( $primary_menu, $about );
	if ( post_type_exists( 'reference' ) ) {
		wp_update_nav_menu_item(
			$primary_menu,
			0,
			array(
				'menu-item-title'  => 'Réalisations',
				'menu-item-object' => 'reference',
				'menu-item-type'   => 'post_type_archive',
				'menu-item-status' => 'publish',
			)
		);
	}
	$add_page( $primary_menu, $blog );
	$add_page( $primary_menu, $contact );

	$footer_menu = wp_create_nav_menu( 'Démo – Pied de page' );
	$add_page( $footer_menu, $legal );
	$add_page( $footer_menu, $privacy );
	$add_page( $footer_menu, $contact );

	$state['menus'] = array( $primary_menu, $footer_menu );
	set_theme_mod(
		'nav_menu_locations',
		array_merge(
			(array) $state['menu_locations'],
			array(
				'primary' => $primary_menu,
				'footer'  => $footer_menu,
			)
		)
	);

	update_option( STARTER_DEMO_STATE, $state, false );
	flush_rewrite_rules();
}

/*
 * ---------------------------------------------------------------------------
 * Lancement
 * ---------------------------------------------------------------------------
 */

if ( in_array( 'reset', $args ?? array(), true ) ) {
	starter_demo_reset();
	WP_CLI::success( 'Démo supprimée, réglages restaurés.' );
	return;
}

// Démo déjà présente : on supprime tout SAUF les pages, qui seront mises à jour.
if ( get_option( STARTER_DEMO_STATE ) ) {
	starter_demo_reset( true );
}

starter_demo_create();
WP_CLI::success( 'Démo créée : ' . home_url( '/' ) );
