<?php get_header(); ?>

<main class="site-main">
    <?php while (have_posts()) : the_post(); ?>
        <article <?php post_class('post-single'); ?>>

            <header class="post-single__header">
                <h1 class="post-single__title"><?php the_title(); ?></h1>

                <div class="post-single__meta">
                    <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                        <?php echo esc_html(get_the_date()); ?>
                    </time>

                    <?php if (has_category()) : ?>
                        <span class="post-single__categories">
                            <?php the_category(', '); ?>
                        </span>
                    <?php endif; ?>
                </div>
            </header>

            <?php if (has_post_thumbnail()) : ?>
                <div class="post-single__thumbnail">
                    <?php the_post_thumbnail('large'); ?>
                </div>
            <?php endif; ?>

            <div class="entry-content">
                <?php the_content(); ?>
            </div>

            <?php if (has_tag()) : ?>
                <footer class="post-single__tags">
                    <?php the_tags('', ', '); ?>
                </footer>
            <?php endif; ?>

        </article>

        <nav class="post-navigation">
            <?php
            previous_post_link('<div class="post-navigation__prev">%link</div>', '← %title');
            next_post_link('<div class="post-navigation__next">%link</div>', '%title →');
            ?>
        </nav>

        <?php
        if (comments_open() || get_comments_number()) {
            comments_template();
        }
        ?>

    <?php endwhile; ?>
</main>

<?php get_footer(); ?>