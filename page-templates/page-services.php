<?php
/* Template Name: Services */
get_header(); ?>

<main class="site-main">

    <?php while (have_posts()):
        the_post(); ?>
        <header class="services-intro">
            <h1><?php the_title(); ?></h1>
            <div class="services-intro__text">
                <?php the_content(); ?>
            </div>
        </header>
    <?php
    endwhile; ?>

    <?php $services = new WP_Query([
        'post_type' => 'service',
        'posts_per_page' => -1,
        'orderby' => 'menu_order',
        'order' => 'ASC',
    ]); ?>

    <?php if ($services->have_posts()): ?>
        <div class="services-list">
            <?php while ($services->have_posts()):

                $services->the_post();

                $price = get_field('price_from');
                ?>
                <?php starter_component('service-card', [
                    'title' => get_the_title(),
                    'description' => get_the_excerpt(),
                    'image_id' => get_post_thumbnail_id(),
                    'url' => get_permalink(),
                    'icon' => get_field('icon'),
                    'price' => $price
                        ? sprintf(__('À partir de %s €', 'starter'), number_format_i18n($price))
                        : '',
                    'duration' => get_field('duration'),
                    'cta_url' => get_field('cta_url'),
                ]); ?>
            <?php
            endwhile; ?>
        </div>
        <?php wp_reset_postdata(); ?>
    <?php endif; ?>

</main>

<?php get_footer(); ?>
