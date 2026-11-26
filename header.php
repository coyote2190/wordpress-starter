<?php
/**
 * En-tête du site : <head>, ouverture du <body>, barre de navigation.
 * Appelé par get_header() en haut de chaque gabarit.
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'flex min-h-screen flex-col antialiased' ); ?>>
<?php wp_body_open(); ?>

<a class="skip-link fixed top-2 left-2 z-50 rounded bg-dark px-4 py-2 text-white" href="#content">
	<?php esc_html_e( 'Aller au contenu', 'starter' ); ?>
</a>

<header class="site-header relative" data-module="nav">
	<div class="container flex items-center justify-between gap-6 py-6">
		<div class="site-branding">
			<?php get_template_part( 'template-parts/site-brand' ); ?>
		</div>

		<?php if ( has_nav_menu( 'primary' ) ) : ?>
			<button
				class="md:hidden"
				type="button"
				aria-controls="primary-menu"
				aria-expanded="false"
				data-nav-toggle
			>
				<span data-nav-icon-open><?php echo starter_icon( 'menu', 'size-6' ); ?></span>
				<span data-nav-icon-close hidden><?php echo starter_icon( 'close', 'size-6' ); ?></span>
				<span class="sr-only"><?php esc_html_e( 'Menu', 'starter' ); ?></span>
			</button>

			<nav
				id="primary-menu"
				class="site-nav max-md:absolute max-md:inset-x-0 max-md:top-full max-md:z-40 max-md:hidden max-md:border-b max-md:border-line max-md:bg-paper max-md:data-open:block"
				aria-label="<?php esc_attr_e( 'Menu principal', 'starter' ); ?>"
				data-nav-menu
			>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'flex flex-col gap-2 text-[15px] max-md:container max-md:py-4 md:flex-row md:items-center md:gap-7',
						'depth'          => 1,
					)
				);
				?>
			</nav>
		<?php endif; ?>
	</div>
</header>

<main id="content" class="site-main flex-1">
