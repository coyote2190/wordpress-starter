<?php
/**
 * Blocs Gutenberg du thème.
 *
 * - enregistre automatiquement chaque dossier de blocks/ qui contient un block.json ;
 * - ajoute une catégorie « Starter » dans l'outil d'insertion de blocs ;
 * - charge les styles Tailwind dans l'éditeur pour que les blocs y ressemblent au site.
 *
 * Voir docs/blocks.md.
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', 'starter_register_blocks' );

/**
 * Enregistre tous les blocs :
 * - blocks/{nom}/block.json               : blocs SCF (PHP) ;
 * - blocks-native/build/{nom}/block.json  : blocs natifs React, compilés par `npm run blocks:build`.
 */
function starter_register_blocks(): void {
	$block_jsons = array_merge(
		glob( STARTER_DIR . '/blocks/*/block.json' ) ?: array(),
		glob( STARTER_DIR . '/blocks-native/build/*/block.json' ) ?: array()
	);

	foreach ( $block_jsons as $block_json ) {
		register_block_type( dirname( $block_json ) );
	}
}

/**
 * Classe du conteneur intérieur d'un bloc selon son alignement.
 *
 * Règle commune à tous les blocs du thème :
 * - « Pleine largeur » : le fond du bloc couvre tout l'écran, mais le contenu
 *   reste aligné sur la grille du site → conteneur `container` à l'intérieur ;
 * - « Aucun » / « Grande largeur » : le bloc a déjà la bonne largeur → rien.
 *
 * @param string|null $align Valeur de l'attribut align ($block['align'] en SCF, $attributes['align'] en natif).
 */
function starter_block_inner_class( ?string $align ): string {
	return 'full' === $align ? 'container' : '';
}

/**
 * La page affichée commence-t-elle par un Hero ou un Hero slider ?
 *
 * Si oui, page.php n'affiche pas le titre de la page : le bloc contient déjà le <h1>.
 * Seulement pour les pages (pas les articles, qui ont leur date et leur titre).
 */
function starter_starts_with_hero(): bool {
	static $result = null;

	if ( null === $result ) {
		$result = false;
		$post   = get_queried_object();

		if ( is_page() && $post instanceof WP_Post && has_blocks( $post ) ) {
			// parse_blocks() renvoie aussi les espaces entre blocs (blockName null) : on les ignore.
			$blocks = array_values( array_filter( parse_blocks( $post->post_content ), static fn( $b ) => null !== $b['blockName'] ) );
			$result = in_array( $blocks[0]['blockName'] ?? '', array( 'starter/hero', 'starter/hero-slider' ), true );
		}
	}

	return $result;
}

/**
 * Titre avec mots mis en valeur, à partir d'une syntaxe simple saisie par le client :
 *   **mot**  → surligné (pastille de couleur)
 *   __mot__  → souligné (trait ondulé)
 *
 * Le texte est échappé AVANT la transformation : seules nos balises sont ajoutées.
 *
 * @param string $text              Texte saisi.
 * @param string $highlight_classes Classes du surlignage.
 * @param string $underline_classes Classes du soulignement.
 * @return string HTML sûr.
 */
function starter_format_title( string $text, string $highlight_classes, string $underline_classes ): string {
	$html = esc_html( $text );
	$html = preg_replace( '/\*\*(.+?)\*\*/u', '<mark class="' . esc_attr( $highlight_classes ) . '">$1</mark>', $html );
	$html = preg_replace( '/__(.+?)__/u', '<span class="' . esc_attr( $underline_classes ) . '">$1</span>', $html );
	return (string) $html;
}

/**
 * Attributs target / rel d'un champ Lien SCF ouvert dans un nouvel onglet.
 *
 * @param array<string, string> $link Champ Lien : [ 'url', 'title', 'target' ].
 * @return string Attributs HTML sûrs (ou chaîne vide).
 */
function starter_link_target( array $link ): string {
	return '_blank' === ( $link['target'] ?? '' ) ? 'target="_blank" rel="noopener"' : '';
}

/**
 * Bouton du thème (pilule), à partir d'un champ Lien SCF.
 *
 * Variantes :
 * - 'icon'   : pilule foncée avec icône dans un rond à gauche (« + Démarrer un projet ») ;
 * - 'simple' : pilule foncée, texte + petite icône à droite (« C'est parti → ») ;
 * - 'link'   : lien souligné, sans fond (« Nos réalisations »).
 *
 * @param array<string, string> $link    Champ Lien : [ 'url', 'title', 'target' ].
 * @param string                $variant 'icon', 'simple' ou 'link'.
 * @param string                $icon    Icône de assets/icons/ (variantes 'icon' et 'simple').
 * @param string                $default Texte si le lien n'a pas de titre.
 * @return string HTML du bouton, ou chaîne vide si le lien n'a pas d'URL.
 */
function starter_button( array $link, string $variant = 'icon', string $icon = 'arrow-right', string $default = '' ): string {
	if ( empty( $link['url'] ) ) {
		return '';
	}

	$label = ( $link['title'] ?? '' ) ?: ( $default ?: __( 'En savoir plus', 'starter' ) );
	$base  = 'inline-flex w-fit items-center gap-2.5 rounded-full font-medium no-underline transition';

	$classes = array(
		'icon'   => $base . ' bg-dark py-2 pr-5.5 pl-2 text-white hover:bg-primary',
		'simple' => $base . ' bg-dark px-5.5 py-3 text-white hover:bg-primary',
		'link'   => $base . ' px-2 py-3.5 underline underline-offset-4 hover:opacity-70',
	);

	$inner = esc_html( $label );
	if ( 'icon' === $variant ) {
		$inner = '<span class="border-[1.5px] border-white/50 inline-flex items-center justify-center rounded-full size-9">' . starter_icon( $icon, 'size-4' ) . '</span>' . $inner;
	} elseif ( 'simple' === $variant ) {
		$inner .= starter_icon( $icon, 'size-4' );
	}

	return sprintf(
		'<a class="%s" href="%s" %s>%s</a>',
		esc_attr( $classes[ $variant ] ?? $classes['icon'] ),
		esc_url( $link['url'] ),
		starter_link_target( $link ),
		$inner
	);
}

add_filter( 'block_categories_all', 'starter_block_category' );

/**
 * Ajoute la catégorie « Starter » en tête de liste dans l'éditeur.
 *
 * @param array<int, array<string, string>> $categories Catégories existantes.
 * @return array<int, array<string, string>>
 */
function starter_block_category( array $categories ): array {
	array_unshift(
		$categories,
		array(
			'slug'  => 'starter',
			'title' => __( 'Starter', 'starter' ),
		)
	);
	return $categories;
}

add_action( 'enqueue_block_assets', 'starter_editor_styles' );

/**
 * Styles Tailwind dans l'éditeur Gutenberg.
 *
 * `enqueue_block_assets` charge le CSS DANS l'iframe de l'éditeur (là où le
 * contenu est affiché), sans toucher à l'interface de WordPress autour.
 * Ce hook est aussi appelé côté visiteur : on se limite à l'admin.
 */
function starter_editor_styles(): void {
	if ( is_admin() ) {
		starter_vite_enqueue( 'starter-editor', 'assets/src/css/editor.css' );
	}
}
