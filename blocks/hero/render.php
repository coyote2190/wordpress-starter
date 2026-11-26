<?php
/**
 * Rendu du bloc Hero (front ET aperçu dans l'éditeur) : grand titre à gauche,
 * texte et bouton à droite, puis une grande image aux coins arrondis.
 * Même mise en page que le haut du Hero slider, avec une seule image.
 *
 * Variables fournies par SCF :
 *
 * @var array  $block      Réglages du bloc (block.json + attributs : align, anchor, className…).
 * @var string $content    Contenu des blocs enfants (inutilisé ici).
 * @var bool   $is_preview true dans l'éditeur.
 * @var int    $post_id    ID du contenu qui contient le bloc.
 *
 * Champs : scf-json/group_starter_block_hero.json
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

$title  = (string) starter_field( 'title' );
$text   = starter_field( 'text' );
$button = starter_field( 'button', false, array() ); // Champ Lien : url, title, target.
$image  = (int) starter_field( 'image', false, 0 );  // Champ Image, format de retour : ID.

// Bloc vide dans l'éditeur : on guide le rédacteur au lieu d'afficher un cadre vide.
if ( '' === $title ) {
	if ( $is_preview ) {
		echo '<p class="border border-dashed border-gray-300 p-8 rounded text-center text-gray-500">'
			. esc_html__( 'Hero : remplissez le titre dans le panneau de droite.', 'starter' )
			. '</p>';
	}
	return;
}

// get_block_wrapper_attributes() ajoute les classes et attributs gérés par
// WordPress : wp-block-starter-hero, alignwide, ancre (id), classes CSS additionnelles.
// not-prose : le bloc ignore la mise en forme « prose » de page.php.
$wrapper = get_block_wrapper_attributes( array( 'class' => 'mb-16 md:mb-24 mt-6 not-prose' ) );
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput -- échappé par WordPress. ?>>
<div class="<?php echo esc_attr( starter_block_inner_class( $block['align'] ?? null ) ); ?>">
	<div class="flex flex-wrap gap-x-16 gap-y-8 items-end <?php echo $image ? 'mb-10' : ''; ?>">
		<h1 class="flex-[2_1_32rem] leading-[.98] text-[clamp(2.75rem,6vw,5.5rem)] text-balance tracking-[-.045em]">
			<?php echo starter_format_title( $title, 'title-mark', 'underline decoration-secondary decoration-wavy underline-offset-8' ); // phpcs:ignore WordPress.Security.EscapeOutput -- échappé dans starter_format_title(). ?>
		</h1>

		<?php if ( $text || ! empty( $button['url'] ) ) : ?>
			<div class="flex flex-[1_1_18rem] flex-col gap-6 items-start" <?php echo $is_preview ? 'inert' : ''; ?>>
				<?php if ( $text ) : ?>
					<p class="leading-normal text-[1.1875rem] text-muted"><?php echo esc_html( $text ); ?></p>
				<?php endif; ?>
				<?php echo starter_button( is_array( $button ) ? $button : array(), 'icon', 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput -- échappé dans starter_button(). ?>
			</div>
		<?php endif; ?>
	</div>

	<?php if ( $image ) : ?>
		<?php
		echo wp_get_attachment_image(
			$image,
			'full',
			false,
			array(
				'class'         => 'bg-secondary h-[clamp(16rem,42vw,34rem)] object-cover rounded-[2rem] w-full',
				'sizes'         => '(min-width: 80rem) 80rem, 100vw',
				'fetchpriority' => 'high', // Image du haut de page : priorité de chargement.
				'loading'       => 'eager',
			)
		);
		?>
	<?php endif; ?>
</div>
</section>
