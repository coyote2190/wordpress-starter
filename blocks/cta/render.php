<?php
/**
 * Rendu du bloc « Appel à l'action ».
 *
 * Titre : **mot** = surligné, __mot__ = souligné (starter_format_title()).
 * Action « e-mail » : formulaire POST vers la page cible (ex. /contact/),
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
$people_layout = 'float' === starter_field( 'people_layout', false, 'stack' ) ? 'float' : 'stack';
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
	'title-mark [--mark-color:var(--cta-accent)] ' . ( starter_color_is_light( $accent ) ? 'text-dark' : 'text-white' ),
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
		'class' => 'not-prose relative isolate overflow-hidden bg-(--cta-bg) py-20 md:py-30 ' . ( $dark_bg ? 'text-white' : 'text-dark' ) . ( $is_full ? '' : ' my-16 rounded-3xl px-6' ),
		'style' => "--cta-bg: var(--wp--preset--color--{$bg}); --cta-accent: var(--wp--preset--color--{$accent});",
	)
);
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput -- échappé par WordPress. ?>>
	<?php foreach ( 'float' === $people_layout ? $people : array() as $i => $person ) : ?>
		<?php // Décor : masqué aux lecteurs d'écran, affiché seulement sur grand écran. ?>
		<div class="absolute hidden flex-col items-center gap-2 motion-safe:animate-float lg:flex <?php echo esc_attr( $slots[ $i ] ); ?>" style="animation-delay: <?php echo esc_attr( (string) ( $i * -1.5 ) ); ?>s" aria-hidden="true">
			<?php echo wp_get_attachment_image( (int) $person['image'], 'thumbnail', false, array( 'class' => 'size-20 rounded-full object-cover shadow-lg ring-4 ring-white', 'alt' => '' ) ); ?>
			<?php if ( ! empty( $person['name'] ) ) : ?>
				<span class="rounded-full bg-white px-3 py-1 text-sm text-dark shadow"><?php echo esc_html( $person['name'] ); ?></span>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>

	<div class="relative mx-auto flex max-w-[55rem] flex-col items-center gap-7 text-center <?php echo esc_attr( starter_block_inner_class( $block['align'] ?? null ) ); ?>">
		<?php if ( $people && 'stack' === $people_layout ) : ?>
			<?php // Pile d'avatars : décorative (aria-hidden), bordure de la couleur du fond pour les détacher. ?>
			<div class="flex -space-x-3" aria-hidden="true">
				<?php foreach ( $people as $person ) : ?>
					<?php echo wp_get_attachment_image( (int) $person['image'], 'thumbnail', false, array( 'class' => 'size-13 rounded-full border-3 border-(--cta-bg) object-cover', 'alt' => '' ) ); ?>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<h2 class="text-[clamp(2.5rem,5.6vw,5rem)] leading-none tracking-[-.045em] text-balance">
			<?php echo $title_html; // phpcs:ignore WordPress.Security.EscapeOutput -- échappé dans starter_format_title(). ?>
		</h2>

		<?php if ( $text ) : ?>
			<p class="max-w-[30rem] text-[1.1875rem] leading-normal opacity-80"><?php echo esc_html( $text ); ?></p>
		<?php endif; ?>

		<?php if ( ! empty( $button['url'] ) ) : ?>
			<?php $label = ( $button['title'] ?? '' ) ?: __( 'Commencer', 'starter' ); ?>

			<?php if ( 'email' === $action ) : ?>
				<?php
				// Formulaire POST vers la page cible : l'adresse pré-remplit le formulaire de contact.
			// POST et non GET : l'e-mail ne doit pas apparaître dans l'URL (logs, statistiques, Referer).
				// Dans l'éditeur : aperçu non cliquable.
				?>
				<form class="flex w-full max-w-[32.5rem] items-center gap-2 rounded-full bg-white p-1.5" method="post" action="<?php echo esc_url( $button['url'] ); ?>" <?php echo $is_preview ? 'inert' : ''; ?>>
					<label class="sr-only" for="<?php echo esc_attr( $block['id'] ?? 'cta' ); ?>-email"><?php esc_html_e( 'Votre adresse e-mail', 'starter' ); ?></label>
					<input
						id="<?php echo esc_attr( $block['id'] ?? 'cta' ); ?>-email"
						class="min-w-0 flex-1 bg-transparent px-4.5 text-dark placeholder:text-soft focus:outline-none"
						type="email"
						name="email"
						autocomplete="email"
						required
						placeholder="<?php echo esc_attr( $hint ); ?>"
					>
					<button class="inline-flex shrink-0 items-center gap-2 rounded-full bg-dark px-5.5 py-3.5 font-medium whitespace-nowrap text-white transition hover:bg-primary" type="submit">
						<?php echo esc_html( $label ); ?>
						<?php echo starter_icon( 'arrow-right', 'size-4' ); ?>
					</button>
				</form>
			<?php else : ?>
				<?php echo starter_button( $button, 'icon', 'arrow-right', $label ); // phpcs:ignore WordPress.Security.EscapeOutput -- échappé dans starter_button(). ?>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</section>
