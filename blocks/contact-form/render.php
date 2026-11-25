<?php
/**
 * Rendu du bloc « Formulaire de contact ».
 * Le bloc n'a pas de champs : il place simplement le formulaire dans la page.
 *
 * @var array $block      Réglages du bloc.
 * @var bool  $is_preview true dans l'éditeur.
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

$wrapper = get_block_wrapper_attributes( array( 'class' => 'not-prose my-10' ) );
?>
<div <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput -- échappé par WordPress. ?>>
	<?php if ( $is_preview ) : ?>
		<?php // Dans l'éditeur : aperçu non cliquable. ?>
		<div class="pointer-events-none" inert>
			<?php get_template_part( 'template-parts/contact-form' ); ?>
		</div>
	<?php else : ?>
		<?php get_template_part( 'template-parts/contact-form' ); ?>
	<?php endif; ?>
</div>
