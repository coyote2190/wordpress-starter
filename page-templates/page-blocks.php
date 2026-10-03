<?php
/* Template Name: Pleine largeur (blocs) */

/**
 * Page construite avec Gutenberg : pas de titre ni de conteneur imposés,
 * les blocs gèrent leur largeur (normal / large / pleine largeur).
 * Utilisé aussi par front-page.php pour la page d'accueil.
 */

get_header(); ?>

<main id="main">
    <?php while (have_posts()):
        the_post(); ?>
        <div class="entry-content blocks">
            <?php the_content(); ?>
        </div>
    <?php
    endwhile; ?>
</main>

<?php get_footer(); ?>
