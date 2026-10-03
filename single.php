<?php get_header(); ?>

<main id="main">
    <?php while (have_posts()):
        the_post(); ?>
        <article <?php post_class('wrap-narrow py-16'); ?>>

            <header>
                <h1 class="text-[2rem] md:text-[2.5rem]"><?php the_title(); ?></h1>

                <div class="text-muted mb-8 flex gap-4 text-sm">
                    <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                        <?php echo esc_html(get_the_date()); ?>
                    </time>

                    <?php if (has_category()): ?>
                        <span>
                            <?php the_category(', '); ?>
                        </span>
                    <?php endif; ?>
                </div>
            </header>

            <?php if (has_post_thumbnail()): ?>
                <div class="mb-8">
                    <?php the_post_thumbnail('large'); ?>
                </div>
            <?php endif; ?>

            <div class="entry-content">
                <?php the_content(); ?>
            </div>

            <?php if (has_tag()): ?>
                <footer class="border-line mt-8 border-t pt-6 text-sm">
                    <?php the_tags('', ', '); ?>
                </footer>
            <?php endif; ?>

        </article>

        <nav class="wrap-narrow flex justify-between gap-6 py-8">
            <?php
            previous_post_link('<div>%link</div>', '← %title');
            next_post_link('<div>%link</div>', '%title →');
            ?>
        </nav>

        <?php if (comments_open() || get_comments_number()) {
            comments_template();
        } ?>

    <?php
    endwhile; ?>
</main>

<?php get_footer(); ?>
