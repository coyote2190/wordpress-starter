<?php get_header(); ?>

<main id="main">
    <?php while (have_posts()):
        the_post(); ?>
        <article <?php post_class('wrap-narrow py-16'); ?>>
            <h1 class="mb-8 text-[2rem] md:text-[2.5rem]"><?php the_title(); ?></h1>

            <?php if (has_post_thumbnail()): ?>
                <div class="mb-8">
                    <?php the_post_thumbnail('large'); ?>
                </div>
            <?php endif; ?>

            <div class="entry-content">
                <?php the_content(); ?>
            </div>
        </article>
    <?php
    endwhile; ?>
</main>

<?php get_footer(); ?>
