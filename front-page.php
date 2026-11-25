<?php
/**
 * Page d'accueil — projet resto « Chez Marcel »
 *
 * - Réglages > Lecture > « Une page statique » : on affiche cette page d'accueil.
 * - « Les derniers articles » : hiérarchie classique (home.php / index.php).
 *
 * Le texte est écrit directement ici (version simple). Les photos sont
 * dans assets/images/.
 */

if (get_option('show_on_front') !== 'page') {
    require get_home_template() ?: get_index_template();
    return;
}

get_header(); ?>

<main id="main">

    <!-- 1. Hero -->
    <section class="bg-brand text-cream">
        <div class="wrap grid items-center gap-10 py-16 md:grid-cols-2 md:py-24">
            <div>
                <h1 class="mb-6 text-5xl leading-none font-bold uppercase md:text-7xl">
                    La cuisine<br>faite maison<br>à Lyon
                </h1>
                <p class="mb-8 text-lg font-medium uppercase">Depuis 1970… à peu près.</p>
                <a href="<?php echo esc_url(home_url('/menu')); ?>"
                   class="bg-cream text-brand inline-block rounded-full px-8 py-3 font-bold no-underline">
                    Voir la carte
                </a>
            </div>

            <img
                src="<?php echo esc_url(get_theme_file_uri('assets/images/pizza.jpg')); ?>"
                alt="Une part de pizza au pepperoni, fromage filant"
                class="aspect-[4/3] w-full rotate-3 rounded-lg object-cover object-[50%_70%]"
                width="1600" height="1200" fetchpriority="high"
            >
        </div>
    </section>

    <!-- 2. Bandeau défilant -->
    <?php $marquee = ['Fait maison chaque jour', 'Produits locaux', 'Ouvert 7j/7', 'Sur place ou à emporter']; ?>
    <div class="bg-canvas text-brand overflow-hidden py-3 font-bold uppercase" aria-hidden="true">
        <div class="marquee-track flex w-max gap-8 whitespace-nowrap">
            <?php for ($i = 0; $i < 4; $i++): ?>
                <?php foreach ($marquee as $text): ?>
                    <span><?php echo esc_html($text); ?></span>
                    <span>★</span>
                <?php endforeach; ?>
            <?php endfor; ?>
        </div>
    </div>

    <!-- 3. Notre spécialité : fond foncé à gauche, texte à droite -->
    <section class="grid md:grid-cols-2">
        <img
            src="<?php echo esc_url(get_theme_file_uri('assets/images/spritz.jpg')); ?>"
            alt="Un spritz orange tenu à deux mains sur une nappe à carreaux"
            class="h-full max-h-150 min-h-80 w-full object-cover"
            width="1067" height="1600" loading="lazy"
        >

        <div class="bg-cream flex flex-col items-start justify-center gap-4 p-10 md:p-16">
            <h2 class="text-brand mb-0 text-4xl font-bold uppercase md:text-5xl">
                Le goût de chez nous, dans votre assiette
            </h2>
            <p class="max-w-md">
                Depuis plus de 50 ans, la famille prépare ses recettes à la main, chaque matin.
                Des produits simples, de saison, cuisinés sans raccourci.
            </p>
            <a href="<?php echo esc_url(home_url('/menu')); ?>"
               class="bg-brand text-cream inline-block rounded-full px-6 py-3 font-bold no-underline">
                Découvrir la carte
            </a>
        </div>
    </section>

    <!-- 4. Venez nous voir -->
    <section class="bg-brand text-cream">
        <div class="wrap grid items-center gap-10 py-16 md:grid-cols-2">
            <img
                src="<?php echo esc_url(get_theme_file_uri('assets/images/pique-nique.jpg')); ?>"
                alt="Fraises, croissant et vin posés sur une nappe à carreaux rouges"
                class="aspect-[4/3] w-full rounded-lg object-cover object-[50%_75%]"
                width="1200" height="1600" loading="lazy"
            >

            <div>
                <h2 class="mb-6 text-4xl font-bold uppercase md:text-5xl">Venez nous voir</h2>
                <p class="mb-2">12 rue de la République, 69002 Lyon</p>
                <p class="mb-8">Mardi – dimanche : 12h–14h30 / 19h–22h30</p>
                <a href="<?php echo esc_url(home_url('/contact')); ?>"
                   class="bg-cream text-brand inline-block rounded-full px-6 py-3 font-bold no-underline">
                    Réserver une table
                </a>
            </div>
        </div>
    </section>

</main>

<?php get_footer(); ?>
