<?php get_header(); ?>

<main id="main">
    <section class="wrap-narrow py-16 text-center">
        <h1 class="text-[2rem]"><?php esc_html_e('Page introuvable', 'starter'); ?></h1>

        <p class="text-muted mb-8">
            <?php esc_html_e(
                'La page que vous cherchez n\'existe pas ou a été déplacée.',
                'starter',
            ); ?>
        </p>

        <div class="mb-8">
            <?php get_search_form(); ?>
        </div>

        <a href="<?php echo esc_url(home_url('/')); ?>" class="btn">
            <?php esc_html_e('Retour à l\'accueil', 'starter'); ?>
        </a>
    </section>
</main>

<?php get_footer(); ?>
