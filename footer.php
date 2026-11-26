<?php
/**
 * Pied de page : fermeture du <main>, footer, wp_footer().
 * Appelé par get_footer() en bas de chaque gabarit.
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;
?>
</main>

<footer class="bg-dark site-footer text-white">
	<div class="container flex flex-col gap-12 pb-8 pt-18">
		<div class="flex flex-wrap gap-8 justify-between leading-relaxed">
			<div class="flex flex-col gap-1 text-[#b5b5b5]">
				<?php get_template_part( 'template-parts/site-brand', null, array( 'class' => 'mb-3 text-white text-xl' ) ); ?>
				<?php get_template_part( 'template-parts/site-contact' ); ?>
			</div>

			<?php if ( has_nav_menu( 'footer' ) ) : ?>
				<nav class="site-nav" aria-label="<?php esc_attr_e( 'Menu du pied de page', 'starter' ); ?>">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'footer',
							'container'      => false,
							'menu_class'     => 'flex flex-wrap gap-x-7 gap-y-2',
							'depth'          => 1,
						)
					);
					?>
				</nav>
			<?php endif; ?>
		</div>

		<div class="border-t border-white/15 flex flex-wrap gap-4 justify-between pt-6 text-sm text-white/55">
			<p>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?></p>
			<?php get_template_part( 'template-parts/site-socials' ); ?>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
