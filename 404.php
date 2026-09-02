<?php get_header(); ?>

<main class="site-main">
    <section class="error-404">
        <h1 class="error-404__title"><?php esc_html_e('Page introuvable', 'starter'); ?></h1>

        <p class="error-404__text">
            <?php esc_html_e(
                'La page que vous cherchez n\'existe pas ou a été déplacée.',
                'starter',
            ); ?>
        </p>

        <div class="error-404__search">
            <?php get_search_form(); ?>
        </div>

        <a href="<?php echo esc_url(home_url('/')); ?>" class="btn error-404__link">
            <?php esc_html_e('Retour à l\'accueil', 'starter'); ?>
        </a>
    </section>
</main>

<?php get_footer(); ?>
