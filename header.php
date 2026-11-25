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

<header class="bg-brand text-cream relative">
    <div class="wrap flex items-center justify-between gap-6 py-4 md:grid md:grid-cols-[1fr_auto_1fr]">

        <!-- Logo -->
        <div>
            <?php if (has_custom_logo()): ?>
                <?php the_custom_logo(); ?>
            <?php else: ?>
                <a class="font-display text-xl font-bold no-underline" href="<?php echo esc_url(
                    home_url('/'),
                ); ?>" rel="home">
                    <?php bloginfo('name'); ?>
                </a>
            <?php endif; ?>
        </div>

        <!-- Menu centré (déroulant en mobile) -->
        <nav
            class="bg-brand-dark absolute top-full right-0 left-0 z-10 hidden p-4 md:static md:block md:bg-transparent md:p-0"
            id="primary-nav"
            aria-label="<?php esc_attr_e('Navigation principale', 'starter'); ?>"
            data-nav
        >
            <?php wp_nav_menu([
                'theme_location' => 'primary',
                'container' => false,
                'menu_class' =>
                    'm-0 flex list-none flex-col gap-2 p-0 text-sm font-bold uppercase md:flex-row md:gap-8 ' .
                    '[&_a]:block [&_a]:py-2 [&_a]:no-underline [&_a:hover]:underline',
                'fallback_cb' => false,
                'depth' => 2,
            ]); ?>
        </nav>

        <!-- Bouton + burger mobile -->
        <div class="flex items-center justify-end gap-3">
            <a
                href="<?php echo esc_url(home_url('/contact')); ?>"
                class="bg-cream text-brand inline-block rounded-full px-5 py-2 text-sm font-bold uppercase no-underline"
            >
                <?php esc_html_e('Réserver', 'starter'); ?>
            </a>

            <button
                class="inline-flex items-center gap-2 border border-current bg-transparent px-3 py-2 md:hidden"
                type="button"
                aria-expanded="false"
                aria-controls="primary-nav"
                data-nav-toggle
            >
                <span class="sr-only"><?php esc_html_e('Menu', 'starter'); ?></span>
                <span class="burger" aria-hidden="true"></span>
            </button>
        </div>

    </div>
</header>
