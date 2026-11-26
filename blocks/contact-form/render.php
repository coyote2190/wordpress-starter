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

$wrapper = get_block_wrapper_attributes( array( 'class' => '@container my-10 not-prose' ) );
?>
<div <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput -- échappé par WordPress. ?>>
<div class="<?php echo esc_attr( starter_block_inner_class( $block['align'] ?? null ) ); ?>">
	<div class="gap-4 grid <?php echo $cards ? '@3xl:grid-cols-[minmax(0,1fr)_minmax(0,1.7fr)]' : ''; ?>">
		<?php if ( $cards ) : ?>
			<ul class="flex flex-col gap-4">
				<?php foreach ( $cards as $card ) : ?>
					<li class="bg-white border border-light flex gap-4 has-[a:hover]:border-dark items-center p-6 relative rounded-3xl transition">
						<span class="bg-secondary inline-flex items-center justify-center rounded-full shrink-0 size-12">
							<?php echo starter_icon( $card['icon'], 'size-5' ); ?>
						</span>
						<span class="flex flex-col gap-0.5 min-w-0">
							<span class="font-medium text-[13px] text-soft tracking-[.06em] uppercase"><?php echo esc_html( $card['label'] ); ?></span>
							<?php if ( $card['url'] ) : ?>
								<?php // after:absolute after:inset-0 : toute la carte est cliquable. ?>
								<a class="after:absolute after:inset-0 break-words font-heading font-medium no-underline text-lg" href="<?php echo esc_attr( $card['url'] ); ?>"><?php echo esc_html( $card['value'] ); ?></a>
							<?php else : ?>
								<span class="font-heading font-medium text-lg"><?php echo esc_html( $card['value'] ); ?></span>
							<?php endif; ?>
						</span>
					</li>
				<?php endforeach; ?>

			</ul>
		<?php endif; ?>

		<div class="bg-white border border-light md:p-10 p-6 rounded-[2rem]">
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
