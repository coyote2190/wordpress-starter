<?php get_header(); ?>

<main class="site-main">

    <header class="archive-header">
        <h1 class="archive-title">
            <?php printf(
                /* translators: %s: search query */
                esc_html__('Résultats pour : %s', 'starter'),
                '<span>' . esc_html(get_search_query()) . '</span>',
            ); ?>
        </h1>

        <?php if (have_posts()): ?>
            <p class="archive-count">
                <?php printf(
                    /* translators: %d: number of results */
                    esc_html(_n('%d résultat', '%d résultats', $wp_query->found_posts, 'starter')),
                    (int) $wp_query->found_posts,
                ); ?>
            </p>
        <?php endif; ?>

        <div class="archive-search">
            <?php get_search_form(); ?>
        </div>
    </header>

    <?php if (have_posts()): ?>

        <div class="archive-list">
            <?php while (have_posts()):
                the_post(); ?>
                <article <?php post_class('archive-item'); ?>>

                    <?php if (has_post_thumbnail()): ?>
                        <a href="<?php the_permalink(); ?>" class="archive-item__thumbnail">
                            <?php the_post_thumbnail('medium'); ?>
                        </a>
                    <?php endif; ?>

                    <h2 class="archive-item__title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h2>

                    <div class="archive-item__excerpt">
                        <?php the_excerpt(); ?>
                    </div>

                </article>
            <?php
            endwhile; ?>
        </div>

        <?php the_posts_pagination([
            'mid_size' => 2,
            'prev_text' => '←',
            'next_text' => '→',
        ]); ?>

    <?php else: ?>
        <p class="archive-empty">
            <?php esc_html_e('Aucun résultat ne correspond à votre recherche.', 'starter'); ?>
        </p>
    <?php endif; ?>

</main>

<?php get_footer(); ?>
