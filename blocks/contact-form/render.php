<?php
/**
 * Rendu du bloc « Formulaire de contact ».
 *
 * Le bloc n'a pas de champs : il affiche les coordonnées de la page « Infos du site »
 * (téléphone, e-mail, adresse) en cartes, à côté du formulaire.
 * Deux colonnes dès que le bloc est assez large (requête de conteneur `@3xl`),
 * l'une sous l'autre sinon.
 *
 * @var array $block      Réglages du bloc.
 * @var bool  $is_preview true dans l'éditeur.
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

$phone   = (string) starter_option( 'phone' );
$email   = (string) starter_option( 'email' );
$address = (string) starter_option( 'address' );

$cards = array_filter(
	array(
		$phone ? array(
			'icon'  => 'phone',
			'label' => __( 'Téléphone', 'starter' ),
			'value' => $phone,
			'url'   => 'tel:' . preg_replace( '/[^0-9+]/', '', $phone ),
		) : null,
		$email ? array(
			'icon'  => 'mail',
			'label' => __( 'E-mail', 'starter' ),
			// antispambot() encode l'adresse pour gêner les robots collecteurs.
			'value' => antispambot( $email ),
			'url'   => 'mailto:' . antispambot( $email ),
		) : null,
		$address ? array(
			'icon'  => 'map-pin',
			'label' => __( 'Adresse', 'starter' ),
			'value' => implode( ', ', array_filter( array_map( 'trim', preg_split( '/\R|<br\s*\/?>/i', $address ) ) ) ),
			'url'   => '',
		) : null,
	)
);

$wrapper = get_block_wrapper_attributes( array( 'class' => 'not-prose my-10 @container' ) );
?>
<div <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput -- échappé par WordPress. ?>>
<div class="<?php echo esc_attr( starter_block_inner_class( $block['align'] ?? null ) ); ?>">
	<div class="grid gap-4 <?php echo $cards ? '@3xl:grid-cols-[minmax(0,1fr)_minmax(0,1.7fr)]' : ''; ?>">
		<?php if ( $cards ) : ?>
			<ul class="flex flex-col gap-4">
				<?php foreach ( $cards as $card ) : ?>
					<li class="relative flex items-center gap-4 rounded-3xl border border-light bg-white p-6 transition has-[a:hover]:border-dark">
						<span class="inline-flex size-12 shrink-0 items-center justify-center rounded-full bg-secondary">
							<?php echo starter_icon( $card['icon'], 'size-5' ); ?>
						</span>
						<span class="flex min-w-0 flex-col gap-0.5">
							<span class="text-[13px] font-medium tracking-[.06em] text-soft uppercase"><?php echo esc_html( $card['label'] ); ?></span>
							<?php if ( $card['url'] ) : ?>
								<?php // after:absolute after:inset-0 : toute la carte est cliquable. ?>
								<a class="font-heading text-lg font-medium break-words no-underline after:absolute after:inset-0" href="<?php echo esc_attr( $card['url'] ); ?>"><?php echo esc_html( $card['value'] ); ?></a>
							<?php else : ?>
								<span class="font-heading text-lg font-medium"><?php echo esc_html( $card['value'] ); ?></span>
							<?php endif; ?>
						</span>
					</li>
				<?php endforeach; ?>

			</ul>
		<?php endif; ?>

		<div class="rounded-[2rem] border border-light bg-white p-6 md:p-10">
			<?php if ( $is_preview ) : ?>
				<?php // Dans l'éditeur : aperçu non cliquable. ?>
				<div class="pointer-events-none" inert>
					<?php get_template_part( 'template-parts/contact-form' ); ?>
				</div>
			<?php else : ?>
				<?php get_template_part( 'template-parts/contact-form' ); ?>
			<?php endif; ?>
		</div>
	</div>
</div>
</div>
