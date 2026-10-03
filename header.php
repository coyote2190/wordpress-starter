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

<header class="border-line relative border-b">
    <div class="wrap flex items-center justify-between gap-6 py-4">

        <div>
            <?php if (has_custom_logo()): ?>
                <?php the_custom_logo(); ?>
            <?php else: ?>
                <a class="font-bold no-underline" href="<?php echo esc_url(
                    home_url('/'),
                ); ?>" rel="home">
                    <?php bloginfo('name'); ?>
                </a>
            <?php endif; ?>
        </div>

        <button
            class="inline-flex items-center gap-2 border border-current bg-transparent px-4 py-2 md:hidden"
            type="button"
            aria-expanded="false"
            aria-controls="primary-nav"
            data-nav-toggle
        >
            <span><?php esc_html_e('Menu', 'starter'); ?></span>
            <span class="burger" aria-hidden="true"></span>
        </button>

        <nav
            class="border-line bg-canvas absolute top-full right-0 left-0 hidden border-b p-4 md:static md:block md:border-0 md:p-0"
            id="primary-nav"
            aria-label="<?php esc_attr_e('Navigation principale', 'starter'); ?>"
            data-nav
        >
            <?php wp_nav_menu([
                'theme_location' => 'primary',
                'container' => false,
                'menu_class' =>
                    'm-0 flex list-none flex-col gap-2 p-0 md:flex-row md:gap-6 ' .
                    '[&_a]:block [&_a]:py-2 [&_a]:no-underline [&_a:hover]:underline',
                'fallback_cb' => false,
                'depth' => 2,
            ]); ?>
        </nav>

    </div>
</header>
