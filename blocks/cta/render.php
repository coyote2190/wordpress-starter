<?php
/**
 * Rendu du bloc « Appel à l'action ».
 *
 * Titre : **mot** = surligné, __mot__ = souligné (starter_format_title()).
 * Action « e-mail » : formulaire GET vers la page cible (ex. /contact/?email=…),
 * où le formulaire de contact reprend l'adresse.
 *
 * @var array $block      Réglages du bloc.
 * @var bool  $is_preview true dans l'éditeur.
 *
 * Champs : scf-json/group_starter_block_cta.json
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

$title  = (string) starter_field( 'title' );
$text   = starter_field( 'text' );
$action = (string) starter_field( 'action', false, 'button' );
$button = starter_field( 'button', false, array() );
$button = is_array( $button ) ? $button : array();
$hint   = (string) starter_field( 'placeholder', false, __( 'votre@email.fr', 'starter' ) );
$people = array_slice( array_values( array_filter( (array) starter_field( 'people', false, array() ), static fn( $p ) => ! empty( $p['image'] ) ) ), 0, 4 );

$palette = starter_palette();
$bg      = isset( $palette[ (string) starter_field( 'background', false, 'light' ) ] ) ? starter_field( 'background', false, 'light' ) : 'light';
$accent  = isset( $palette[ (string) starter_field( 'accent', false, 'secondary' ) ] ) ? starter_field( 'accent', false, 'secondary' ) : 'secondary';

if ( '' === $title ) {
	if ( $is_preview ) {
		echo '<p class="rounded border border-dashed border-gray-300 p-8 text-center text-gray-500">'
			. esc_html__( 'Appel à l\'action : remplissez le titre dans le panneau de droite.', 'starter' )
			. '</p>';
	}
	return;
}

$is_full   = 'full' === ( $block['align'] ?? '' );
$dark_bg   = ! starter_color_is_light( $bg );
$title_html = starter_format_title(
	$title,
	// Pastille : texte foncé ou blanc selon la clarté de la couleur d'accent.
	'rounded-2xl bg-(--cta-accent) px-3 box-decoration-clone ' . ( starter_color_is_light( $accent ) ? 'text-dark' : 'text-white' ),
	'underline decoration-(--cta-accent) decoration-wavy decoration-4 underline-offset-8'
);

// Emplacements des personnes autour du titre (grand écran uniquement).
$slots = array(
	'top-10 left-[6%]',
	'top-16 right-[6%]',
	'bottom-14 left-[10%]',
	'bottom-10 right-[9%]',
);

$wrapper = get_block_wrapper_attributes(
	array(
		'class' => 'not-prose relative isolate overflow-hidden bg-(--cta-bg) py-20 md:py-28 ' . ( $dark_bg ? 'text-white' : 'text-dark' ) . ( $is_full ? '' : ' my-16 rounded-3xl px-6' ),
		'style' => "--cta-bg: var(--wp--preset--color--{$bg}); --cta-accent: var(--wp--preset--color--{$accent});",
	)
);
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput -- échappé par WordPress. ?>>
	<?php foreach ( $people as $i => $person ) : ?>
		<?php // Décor : masqué aux lecteurs d'écran, affiché seulement sur grand écran. ?>
		<div class="absolute hidden flex-col items-center gap-2 motion-safe:animate-float lg:flex <?php echo esc_attr( $slots[ $i ] ); ?>" style="animation-delay: <?php echo esc_attr( (string) ( $i * -1.5 ) ); ?>s" aria-hidden="true">
			<?php echo wp_get_attachment_image( (int) $person['image'], 'thumbnail', false, array( 'class' => 'size-20 rounded-full object-cover shadow-lg ring-4 ring-white', 'alt' => '' ) ); ?>
			<?php if ( ! empty( $person['name'] ) ) : ?>
				<span class="rounded-full bg-white px-3 py-1 text-sm text-dark shadow"><?php echo esc_html( $person['name'] ); ?></span>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>

	<div class="relative mx-auto flex max-w-3xl flex-col items-center gap-6 text-center <?php echo esc_attr( starter_block_inner_class( $block['align'] ?? null ) ); ?>">
		<h2 class="text-4xl leading-snug font-bold md:text-6xl md:leading-tight">
			<?php echo $title_html; // phpcs:ignore WordPress.Security.EscapeOutput -- échappé dans starter_format_title(). ?>
		</h2>

		<?php if ( $text ) : ?>
			<p class="max-w-xl text-lg opacity-80"><?php echo esc_html( $text ); ?></p>
		<?php endif; ?>

		<?php if ( ! empty( $button['url'] ) ) : ?>
			<?php $label = ( $button['title'] ?? '' ) ?: __( 'Commencer', 'starter' ); ?>
			<?php $arrow = '<span class="inline-flex size-8 items-center justify-center rounded-full bg-white text-dark">' . starter_icon( 'arrow-right', 'size-4' ) . '</span>'; ?>

			<?php if ( 'email' === $action ) : ?>
				<?php
				// Formulaire GET : envoie vers la page cible avec ?email=… (pré-rempli dans le formulaire de contact).
				// Dans l'éditeur : aperçu non cliquable.
				?>
				<form class="mt-4 flex w-full max-w-lg items-center gap-2 rounded-full bg-white p-1.5 shadow-lg" method="get" action="<?php echo esc_url( $button['url'] ); ?>" <?php echo $is_preview ? 'inert' : ''; ?>>
					<label class="sr-only" for="<?php echo esc_attr( $block['id'] ?? 'cta' ); ?>-email"><?php esc_html_e( 'Votre adresse e-mail', 'starter' ); ?></label>
					<input
						id="<?php echo esc_attr( $block['id'] ?? 'cta' ); ?>-email"
						class="min-w-0 flex-1 bg-transparent px-5 py-2 text-dark placeholder:text-gray-400 focus:outline-none"
						type="email"
						name="email"
						autocomplete="email"
						required
						placeholder="<?php echo esc_attr( $hint ); ?>"
					>
					<button class="inline-flex shrink-0 items-center gap-3 rounded-full bg-dark py-1.5 pr-1.5 pl-5 font-semibold text-white transition hover:bg-dark/90" type="submit">
						<?php echo esc_html( $label ); ?>
						<?php echo $arrow; // phpcs:ignore WordPress.Security.EscapeOutput -- SVG de starter_icon(). ?>
					</button>
				</form>
			<?php else : ?>
				<a class="mt-4 inline-flex items-center gap-3 rounded-full bg-dark py-1.5 pr-1.5 pl-6 font-semibold text-white no-underline shadow-lg transition hover:bg-dark/90" href="<?php echo esc_url( $button['url'] ); ?>" <?php echo '_blank' === ( $button['target'] ?? '' ) ? 'target="_blank" rel="noopener"' : ''; ?>>
					<?php echo esc_html( $label ); ?>
					<?php echo $arrow; // phpcs:ignore WordPress.Security.EscapeOutput -- SVG de starter_icon(). ?>
				</a>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</section>
