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

<header class="site-header relative border-b border-light" data-module="nav">
	<div class="container flex items-center justify-between gap-6 py-4">
		<div class="site-branding">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<a class="font-heading text-xl font-bold text-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
					<?php bloginfo( 'name' ); ?>
				</a>
			<?php endif; ?>
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
				class="site-nav max-md:absolute max-md:inset-x-0 max-md:top-full max-md:z-40 max-md:hidden max-md:border-b max-md:border-light max-md:bg-white max-md:data-open:block"
				aria-label="<?php esc_attr_e( 'Menu principal', 'starter' ); ?>"
				data-nav-menu
			>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'flex flex-col gap-1 max-md:container max-md:py-4 md:flex-row md:gap-6',
						'depth'          => 1,
					)
				);
				?>
			</nav>
		<?php endif; ?>
	</div>
</header>

<main id="content" class="site-main flex-1">
