<?php
/**
 * Liste des articles (fichier de secours de WordPress)
 */
get_header(); ?>

<main id="main">
    <div class="wrap-narrow py-16">
        <h1 class="mb-8">Articles</h1>

        <?php if (have_posts()): ?>
            <?php while (have_posts()):
                the_post(); ?>
                <article <?php post_class('mb-8'); ?>>
                    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <?php the_excerpt(); ?>
                </article>
            <?php
            endwhile; ?>
        <?php else: ?>
            <p>Aucun article pour le moment.</p>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
