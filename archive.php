<?php get_header(); ?>

<main class="site-main">

    <header class="archive-header">
        <h1 class="archive-title"><?php the_archive_title(); ?></h1>

        <?php if (get_the_archive_description()): ?>
            <div class="archive-description">
                <?php the_archive_description(); ?>
            </div>
        <?php endif; ?>
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

                    <time class="archive-item__date" datetime="<?php echo esc_attr(
                        get_the_date('c'),
                    ); ?>">
                        <?php echo esc_html(get_the_date()); ?>
                    </time>

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
        <p><?php esc_html_e('Aucun contenu trouvé.', 'starter'); ?></p>
    <?php endif; ?>

</main>

<?php get_footer(); ?>
