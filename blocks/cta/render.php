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
		echo '<p class="border border-dashed border-gray-300 p-8 rounded text-center text-gray-500">'
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
		'class' => 'bg-(--cta-bg) isolate md:py-30 not-prose overflow-hidden py-20 relative ' . ( $dark_bg ? 'text-white' : 'text-dark' ) . ( $is_full ? '' : ' my-16 rounded-3xl px-6' ),
		'style' => "--cta-bg: var(--wp--preset--color--{$bg}); --cta-accent: var(--wp--preset--color--{$accent});",
	)
);
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput -- échappé par WordPress. ?>>
	<?php foreach ( 'float' === $people_layout ? $people : array() as $i => $person ) : ?>
		<?php // Décor : masqué aux lecteurs d'écran, affiché seulement sur grand écran. ?>
		<div class="absolute flex-col gap-2 hidden items-center lg:flex motion-safe:animate-float <?php echo esc_attr( $slots[ $i ] ); ?>" style="animation-delay: <?php echo esc_attr( (string) ( $i * -1.5 ) ); ?>s" aria-hidden="true">
			<?php echo wp_get_attachment_image( (int) $person['image'], 'thumbnail', false, array( 'class' => 'object-cover ring-4 ring-white rounded-full shadow-lg size-20', 'alt' => '' ) ); ?>
			<?php if ( ! empty( $person['name'] ) ) : ?>
				<span class="bg-white px-3 py-1 rounded-full shadow text-dark text-sm"><?php echo esc_html( $person['name'] ); ?></span>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>

	<div class="flex flex-col gap-7 items-center max-w-[55rem] mx-auto relative text-center <?php echo esc_attr( starter_block_inner_class( $block['align'] ?? null ) ); ?>">
		<?php if ( $people && 'stack' === $people_layout ) : ?>
			<?php // Pile d'avatars : décorative (aria-hidden), bordure de la couleur du fond pour les détacher. ?>
			<div class="flex -space-x-3" aria-hidden="true">
				<?php foreach ( $people as $person ) : ?>
					<?php echo wp_get_attachment_image( (int) $person['image'], 'thumbnail', false, array( 'class' => 'border-(--cta-bg) border-3 object-cover rounded-full size-13', 'alt' => '' ) ); ?>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<h2 class="leading-none text-[clamp(2.5rem,5.6vw,5rem)] text-balance tracking-[-.045em]">
			<?php echo $title_html; // phpcs:ignore WordPress.Security.EscapeOutput -- échappé dans starter_format_title(). ?>
		</h2>

		<?php if ( $text ) : ?>
			<p class="leading-normal max-w-[30rem] opacity-80 text-[1.1875rem]"><?php echo esc_html( $text ); ?></p>
		<?php endif; ?>

		<?php if ( ! empty( $button['url'] ) ) : ?>
			<?php $label = ( $button['title'] ?? '' ) ?: __( 'Commencer', 'starter' ); ?>

			<?php if ( 'email' === $action ) : ?>
				<?php
				// Formulaire POST vers la page cible : l'adresse pré-remplit le formulaire de contact.
			// POST et non GET : l'e-mail ne doit pas apparaître dans l'URL (logs, statistiques, Referer).
				// Dans l'éditeur : aperçu non cliquable.
				?>
				<form class="bg-white flex gap-2 items-center max-w-[32.5rem] p-1.5 rounded-full w-full" method="post" action="<?php echo esc_url( $button['url'] ); ?>" <?php echo $is_preview ? 'inert' : ''; ?>>
					<label class="sr-only" for="<?php echo esc_attr( $block['id'] ?? 'cta' ); ?>-email"><?php esc_html_e( 'Votre adresse e-mail', 'starter' ); ?></label>
					<input
						id="<?php echo esc_attr( $block['id'] ?? 'cta' ); ?>-email"
						class="bg-transparent flex-1 focus:outline-none min-w-0 placeholder:text-soft px-4.5 text-dark"
						type="email"
						name="email"
						autocomplete="email"
						required
						placeholder="<?php echo esc_attr( $hint ); ?>"
					>
					<button class="bg-dark font-medium gap-2 hover:bg-primary inline-flex items-center px-5.5 py-3.5 rounded-full shrink-0 text-white transition whitespace-nowrap" type="submit">
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
