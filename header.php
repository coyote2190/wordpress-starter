<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#main">
    <?php esc_html_e('Aller au contenu', 'starter'); ?>
</a>

<header class="site-header">
    <div class="site-header__inner">

        <div class="site-header__branding">
            <?php if (has_custom_logo()): ?>
                <?php the_custom_logo(); ?>
            <?php else: ?>
                <a class="site-header__title" href="<?php echo esc_url(
                    home_url('/'),
                ); ?>" rel="home">
                    <?php bloginfo('name'); ?>
                </a>
            <?php endif; ?>
        </div>

        <button
            class="site-header__toggle"
            type="button"
            aria-expanded="false"
            aria-controls="primary-nav"
        >
            <span class="site-header__toggle-label"><?php esc_html_e('Menu', 'starter'); ?></span>
            <span class="site-header__toggle-icon" aria-hidden="true"></span>
        </button>

        <nav
            class="site-nav"
            id="primary-nav"
            aria-label="<?php esc_attr_e('Navigation principale', 'starter'); ?>"
        >
            <?php wp_nav_menu([
                'theme_location' => 'primary',
                'container' => false,
                'menu_class' => 'site-nav__list',
                'fallback_cb' => false,
                'depth' => 2,
            ]); ?>
        </nav>

    </div>
</header>