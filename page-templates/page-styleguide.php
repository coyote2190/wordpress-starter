<?php
/* Template Name: Styleguide */

/**
 * Référence visuelle du thème : tokens, composants PHP et kit UI React.
 * Créer une page (non liée au menu) avec ce modèle pour vérifier
 * le thème d'un nouveau client après avoir modifié @theme dans main.css.
 */

get_header(); ?>

<main id="main" class="wrap py-16">

    <h1 class="mb-12 text-[2rem] md:text-[2.5rem]"><?php the_title(); ?></h1>

    <section class="mb-16">
        <h2 class="mb-6 text-2xl">Couleurs</h2>
        <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
            <?php foreach (
                [
                    'ink' => 'bg-ink',
                    'canvas' => 'bg-canvas',
                    'muted' => 'bg-muted',
                    'line' => 'bg-line',
                ]
                as $name => $class
            ): ?>
                <div>
                    <div class="<?php echo esc_attr(
                        $class,
                    ); ?> rounded-ui border-line mb-2 h-16 border"></div>
                    <code><?php echo esc_html($name); ?></code>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="mb-16">
        <h2 class="mb-6 text-2xl">Typographie</h2>
        <div class="entry-content">
            <h1>Titre H1</h1>
            <h2>Titre H2</h2>
            <h3>Titre H3</h3>
            <p>Paragraphe — <a href="#">lien</a>, <strong>gras</strong>.</p>
            <p class="text-muted">Texte secondaire (text-muted).</p>
            <ul>
                <li>Élément de liste</li>
                <li>Élément de liste</li>
            </ul>
        </div>
    </section>

    <section class="mb-16">
        <h2 class="mb-6 text-2xl">Composants PHP (classes partagées)</h2>
        <div class="grid gap-8 md:grid-cols-2">
            <div class="card flex flex-col items-start gap-4">
                <a class="btn" href="#">.btn</a>
                <label class="m-0 w-full">
                    Champ (input)
                    <input type="text" placeholder="Placeholder" class="mt-1">
                </label>
                <label class="m-0 w-full">
                    Liste (select natif)
                    <select class="mt-1">
                        <option>Option</option>
                    </select>
                </label>
            </div>
            <div class="card">
                <h3 class="mb-2 text-xl">.card</h3>
                <p class="text-muted m-0">Encadré partagé entre composants PHP et React.</p>
            </div>
        </div>
    </section>

    <section>
        <h2 class="mb-6 text-2xl">Kit UI React (Base UI)</h2>
        <?php starter_island('ui-demo'); ?>
    </section>

</main>

<?php get_footer(); ?>
