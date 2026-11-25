<?php
/**
 * Rendu du bloc Hero (front ET aperçu dans l'éditeur).
 *
 * Variables fournies par SCF :
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

$title  = starter_field( 'title' );
$text   = starter_field( 'text' );
$button = starter_field( 'button', false, array() ); // Champ Lien : [ 'url', 'title', 'target' ].
$image  = (int) starter_field( 'image', false, 0 );  // Champ Image, format de retour : ID.

// Bloc vide dans l'éditeur : on guide le rédacteur au lieu d'afficher un cadre vide.
if ( ! $title && $is_preview ) {
	echo '<p class="rounded border border-dashed border-gray-300 p-8 text-center text-gray-500">'
		. esc_html__( 'Hero : remplissez le titre dans le panneau de droite.', 'starter' )
		. '</p>';
	return;
}

if ( ! $title ) {
	return;
}

// get_block_wrapper_attributes() ajoute les classes et attributs gérés par
// WordPress : wp-block-starter-hero, alignfull, ancre (id), classes CSS additionnelles.
$wrapper = get_block_wrapper_attributes(
	// not-prose : le bloc ignore la mise en forme « prose » de page.php / single.php.
	array( 'class' => 'not-prose relative isolate overflow-hidden bg-dark text-white' )
);
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput -- échappé par WordPress. ?>>
	<?php if ( $image ) : ?>
		<?php
		echo wp_get_attachment_image(
			$image,
			'full',
			false,
			array(
				'class'         => 'absolute inset-0 -z-10 size-full object-cover opacity-40',
				'fetchpriority' => 'high', // Image du haut de page : priorité de chargement.
				'loading'       => 'eager',
			)
		);
		?>
	<?php endif; ?>

	<div class="container flex min-h-[60vh] flex-col justify-center gap-6 py-20">
		<h1 class="max-w-3xl text-4xl font-bold md:text-6xl"><?php echo esc_html( $title ); ?></h1>

		<?php if ( $text ) : ?>
			<p class="max-w-2xl text-lg text-light md:text-xl"><?php echo esc_html( $text ); ?></p>
		<?php endif; ?>

		<?php if ( ! empty( $button['url'] ) ) : ?>
			<a
				class="inline-flex w-fit items-center gap-2 rounded-md bg-secondary px-6 py-3 font-semibold text-dark no-underline hover:bg-secondary/90"
				href="<?php echo esc_url( $button['url'] ); ?>"
				<?php echo '_blank' === ( $button['target'] ?? '' ) ? 'target="_blank" rel="noopener"' : ''; ?>
			>
				<?php echo esc_html( $button['title'] ?: __( 'En savoir plus', 'starter' ) ); ?>
				<?php echo starter_icon( 'arrow-right', 'size-4' ); ?>
			</a>
		<?php endif; ?>
	</div>
</section>
