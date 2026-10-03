<?php
/* Template Name: Contact */
get_header(); ?>

<main id="main">

    <?php while (have_posts()):
        the_post(); ?>
        <header class="wrap-narrow pt-16 pb-8">
            <h1><?php the_title(); ?></h1>
            <div class="entry-content">
                <?php the_content(); ?>
            </div>
        </header>
    <?php
    endwhile; ?>

    <?php starter_component('contact-form'); ?>

</main>

<?php get_footer(); ?>
