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

<footer class="site-footer mt-16 bg-dark text-light">
	<div class="container flex flex-col gap-6 py-10 md:flex-row md:items-start md:justify-between">
		<div class="flex flex-col gap-4">
			<p class="font-heading text-lg font-bold text-white"><?php bloginfo( 'name' ); ?></p>
			<?php get_template_part( 'template-parts/site-contact' ); ?>
			<p class="text-xs text-light/70">
				&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>
			</p>
		</div>

		<?php if ( has_nav_menu( 'footer' ) ) : ?>
			<nav class="site-nav" aria-label="<?php esc_attr_e( 'Menu du pied de page', 'starter' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'menu_class'     => 'flex flex-wrap gap-x-6 gap-y-2 text-sm',
						'depth'          => 1,
					)
				);
				?>
			</nav>
		<?php endif; ?>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
