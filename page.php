<?php get_header(); ?>

<main class="site-main">
    <?php while (have_posts()) : the_post(); ?>
        <article <?php post_class('page-content'); ?>>
            <h1 class="page-title"><?php the_title(); ?></h1>

            <?php if (has_post_thumbnail()) : ?>
                <div class="page-thumbnail">
                    <?php the_post_thumbnail('large'); ?>
                </div>
            <?php endif; ?>

            <div class="entry-content">
                <?php the_content(); ?>
            </div>
        </article>
    <?php endwhile; ?>
</main>

<?php get_footer(); ?>