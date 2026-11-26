<?php
/**
 * En-tête de page commun (pages, listes, recherche, 404) : même style que le
 * haut de l'accueil — grand titre à gauche, texte à droite.
 *
 * Arguments (get_template_part( 'template-parts/page-header', null, array( … ) )) :
 * - title   (string) Titre. `**mot**` = mot surligné. Obligatoire.
 * - text    (string) Texte d'introduction (facultatif).
 * - eyebrow (string) Petite pastille au-dessus du titre (facultatif).
 * - center  (bool)   Centré (404) au lieu de titre à gauche / texte à droite.
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

$starter_title   = (string) ( $args['title'] ?? '' );
$starter_text    = (string) ( $args['text'] ?? '' );
$starter_eyebrow = (string) ( $args['eyebrow'] ?? '' );
$starter_center  = ! empty( $args['center'] );

if ( '' === $starter_title ) {
	return;
}
?>
<header class="<?php echo esc_attr( $starter_center ? 'mx-auto flex max-w-3xl flex-col items-center gap-6 pt-10 pb-12 text-center md:pt-16' : 'flex flex-wrap items-end justify-between gap-x-16 gap-y-6 pt-6 pb-12 md:pb-16' ); ?>">
	<div class="<?php echo esc_attr( $starter_center ? 'flex flex-col items-center gap-5' : 'flex flex-[2_1_32rem] flex-col items-start gap-5' ); ?>">
		<?php if ( $starter_eyebrow ) : ?>
			<p class="rounded-full border border-light bg-white px-3 py-1 text-[13px] font-medium"><?php echo esc_html( $starter_eyebrow ); ?></p>
		<?php endif; ?>
		<h1 class="text-[clamp(2.75rem,6vw,5.5rem)] leading-[.98] tracking-[-.045em] text-balance">
			<?php echo starter_format_title( $starter_title, 'title-mark', 'underline decoration-secondary decoration-wavy underline-offset-8' ); // phpcs:ignore WordPress.Security.EscapeOutput -- échappé dans starter_format_title(). ?>
		</h1>
	</div>

	<?php if ( $starter_text ) : ?>
		<p class="<?php echo esc_attr( $starter_center ? 'max-w-xl text-[1.1875rem] leading-normal text-muted' : 'flex-[1_1_18rem] text-[1.1875rem] leading-normal text-muted' ); ?>"><?php echo esc_html( $starter_text ); ?></p>
	<?php endif; ?>
</header>
