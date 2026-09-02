<?php
/* Template Name: Contact */
get_header(); ?>

<main class="site-main">

    <?php while (have_posts()):
        the_post(); ?>
        <header class="page-header">
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
