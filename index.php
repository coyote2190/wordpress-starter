<?php get_header(); ?>

<?php
starter_component('hero', [
    'title'       => 'Bienvenue',
    'subtitle'    => 'Un sous-titre engageant ici',
    'button_text' => 'Découvrir',
    'button_url'  => home_url('/contact'),
]);
// get_template_part('template-parts/components/hero', null, [
//     'title'       => 'Bienvenue',
//     'subtitle'    => 'Un sous-titre engageant ici',
//     'button_text' => 'Découvrir',
//     'button_url'  => home_url('/contact'),
// ]);
?>

<main class="site-main">
    <?php if (have_posts()) : ?>
        <?php while (have_posts()) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                <div class="entry-content">
                    <?php the_excerpt(); ?>
                </div>
            </article>
        <?php endwhile; ?>
    <?php else : ?>
        <p><?php esc_html_e('Aucun contenu trouvé.', 'starter'); ?></p>
    <?php endif; ?>
</main>

<?php get_footer(); ?>