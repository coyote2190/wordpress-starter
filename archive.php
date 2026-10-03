<?php get_header(); ?>

<main id="main">

    <header class="wrap-narrow pt-16 pb-8">
        <h1 class="text-[2rem]"><?php the_archive_title(); ?></h1>

        <?php if (get_the_archive_description()): ?>
            <div class="text-muted">
                <?php the_archive_description(); ?>
            </div>
        <?php endif; ?>
    </header>

    <?php if (have_posts()): ?>

        <div class="wrap grid gap-8 md:grid-cols-2 lg:grid-cols-3">
            <?php while (have_posts()):
                the_post(); ?>
                <article <?php post_class(); ?>>

                    <?php if (has_post_thumbnail()): ?>
                        <a href="<?php the_permalink(); ?>" class="mb-4 block">
                            <?php the_post_thumbnail('medium'); ?>
                        </a>
                    <?php endif; ?>

                    <h2 class="mb-2 text-xl [&_a]:no-underline">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h2>

                    <time class="text-muted mb-2 block text-sm" datetime="<?php echo esc_attr(
                        get_the_date('c'),
                    ); ?>">
                        <?php echo esc_html(get_the_date()); ?>
                    </time>

                    <div class="text-muted">
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
            'class' =>
                'wrap flex justify-center gap-2 py-16 ' .
                '[&_.page-numbers]:px-4 [&_.page-numbers]:py-2 [&_.page-numbers]:no-underline [&_.current]:underline',
        ]); ?>

    <?php else: ?>
        <p class="wrap-narrow text-muted py-16 text-center">
            <?php esc_html_e('Aucun contenu trouvé.', 'starter'); ?>
        </p>
    <?php endif; ?>

</main>

<?php get_footer(); ?>
