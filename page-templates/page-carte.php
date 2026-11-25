<?php
/* Template Name: Carte */

/**
 * La carte — plats regroupés par type (entrées, plats, desserts, boissons)
 * Le titre et le texte d'intro viennent de la page (admin > Pages).
 * Les plats viennent du type de contenu « Plat » (admin > Plats).
 */
get_header(); ?>

<main id="main">

    <?php while (have_posts()):
        the_post(); ?>
        <header class="bg-cream text-brand py-16 text-center">
            <div class="wrap">
                <h1 class="mb-2 text-5xl font-bold md:text-6xl"><?php the_title(); ?></h1>
                <div class="entry-content"><?php the_content(); ?></div>
            </div>
        </header>
    <?php endwhile; ?>

    <?php
    // Formules (menus à prix fixe) : admin > Formules
    $formules = get_posts([
        'post_type' => 'formule',
        'numberposts' => -1,
        'orderby' => ['menu_order' => 'ASC', 'title' => 'ASC'],
    ]);
    $courses = ['entrees' => 'Entrée', 'plats' => 'Plat', 'desserts' => 'Dessert'];
    ?>

    <?php if ($formules): ?>
        <section class="wrap py-16">
            <div class="grid gap-8 md:grid-cols-2">
                <?php foreach ($formules as $formule): ?>
                    <article class="bg-cream rounded-lg p-8">
                        <header class="border-brand mb-6 flex items-baseline justify-between gap-4 border-b-2 pb-3">
                            <h2 class="text-brand m-0 text-2xl font-bold">
                                <?php echo esc_html(get_the_title($formule)); ?>
                            </h2>
                            <span class="text-brand text-2xl font-bold whitespace-nowrap">
                                <?php echo esc_html(number_format_i18n((float) starter_field('prix', $formule->ID), 0)); ?> €
                            </span>
                        </header>

                        <?php if ($formule->post_content): ?>
                            <p class="text-muted mt-0 mb-6 text-sm">
                                <?php echo esc_html(wp_strip_all_tags($formule->post_content)); ?>
                            </p>
                        <?php endif; ?>

                        <?php foreach ($courses as $field => $label):
                            $ids = starter_field($field, $formule->ID);
                            if (!$ids) {
                                continue;
                            } ?>
                            <div class="mb-4">
                                <p class="text-brand mb-1 text-sm font-bold uppercase"><?php echo esc_html($label); ?></p>
                                <ul class="m-0 list-none p-0">
                                    <?php foreach ($ids as $plat_id): ?>
                                        <li class="py-0.5">&bull; <?php echo esc_html(get_the_title($plat_id)); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endforeach; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <div class="wrap-narrow py-16">
        <?php
        $types = get_terms(['taxonomy' => 'type_plat', 'orderby' => 'term_id']);
        $has_plats = false;
        $first = true; // seule la première catégorie est ouverte au départ

        foreach ($types as $type):

            $plats = new WP_Query([
                'post_type' => 'plat',
                'posts_per_page' => -1,
                'orderby' => ['menu_order' => 'ASC', 'title' => 'ASC'],
                'tax_query' => [
                    ['taxonomy' => 'type_plat', 'field' => 'term_id', 'terms' => $type->term_id],
                ],
            ]);

            if (!$plats->have_posts()) {
                continue;
            }
            $has_plats = true; ?>

            <details class="group border-brand mb-6 border-b-2 pb-2" <?php echo $first ? 'open' : ''; ?>>
                <summary class="text-brand flex cursor-pointer list-none items-center justify-between gap-4 py-2 [&::-webkit-details-marker]:hidden">
                    <h2 class="m-0 text-3xl font-bold">
                        <?php echo esc_html($type->name); ?>
                        <span class="text-muted text-base font-normal">(<?php echo (int) $plats->post_count; ?>)</span>
                    </h2>
                    <span class="text-2xl transition-transform group-open:rotate-180" aria-hidden="true">▾</span>
                </summary>

                <ul class="m-0 mt-6 list-none p-0">
                    <?php while ($plats->have_posts()):
                        $plats->the_post(); ?>
                        <li class="border-line mb-6 flex gap-4 border-b pb-6 sm:gap-6">
                            <?php if (has_post_thumbnail()): ?>
                                <?php the_post_thumbnail('medium', [
                                    'class' => 'size-24 shrink-0 rounded-lg object-cover sm:size-32',
                                    'loading' => 'lazy',
                                ]); ?>
                            <?php endif; ?>

                            <div class="min-w-0 flex-1">
                                <div class="flex items-baseline justify-between gap-4">
                                    <h3 class="font-display m-0 text-xl font-bold">
                                        <?php the_title(); ?>
                                        <?php if (starter_field('plat_du_jour')): ?>
                                            <span class="bg-brand text-cream ml-2 rounded-full px-3 py-1 align-middle font-sans text-xs font-bold uppercase">
                                                Plat du jour
                                            </span>
                                        <?php endif; ?>
                                    </h3>
                                    <?php if (starter_field('prix') !== '' && starter_field('prix') !== null): ?>
                                        <span class="font-bold whitespace-nowrap">
                                            <?php echo esc_html(number_format_i18n((float) starter_field('prix'), 2)); ?> €
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <?php if (get_the_content()): ?>
                                    <div class="mt-2"><?php the_content(); ?></div>
                                <?php endif; ?>

                                <?php if (starter_field('allergenes')): ?>
                                    <p class="text-muted mt-2 mb-0 text-sm">
                                        Allergènes : <?php echo esc_html(starter_field('allergenes')); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </li>
                    <?php endwhile;
                    wp_reset_postdata(); ?>
                </ul>
            </details>

            <?php $first = false; ?>

        <?php endforeach; ?>

        <?php if (!$has_plats): ?>
            <p class="text-center">La carte arrive bientôt.</p>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
