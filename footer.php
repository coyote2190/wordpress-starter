<footer class="bg-brand-dark text-cream">
    <div class="wrap grid gap-10 py-12 md:grid-cols-3">

        <!-- Nom -->
        <div>
            <p class="font-display mb-2 text-2xl font-bold">
                <?php bloginfo('name'); ?>
            </p>
            <p class="m-0 text-sm opacity-80">La cuisine faite maison, depuis 1970.</p>
        </div>

        <!-- Adresse et horaires (texte écrit ici pour l'instant) -->
        <div>
            <p class="mb-3 text-sm font-bold uppercase">Venez nous voir</p>
            <p class="mb-1">12 rue de la République, 69002 Lyon</p>
            <p class="m-0">Mardi – dimanche<br>12h–14h30 / 19h–22h30</p>
        </div>

        <!-- Menu du footer + bouton -->
        <div>
            <?php wp_nav_menu([
                'theme_location' => 'footer',
                'container' => false,
                'menu_class' =>
                    'm-0 mb-6 flex list-none flex-col gap-2 p-0 text-sm font-bold uppercase ' .
                    '[&_a]:no-underline [&_a:hover]:underline',
                'fallback_cb' => false,
            ]); ?>

            <a href="<?php echo esc_url(home_url('/contact')); ?>"
               class="bg-cream text-brand inline-block rounded-full px-5 py-2 text-sm font-bold uppercase no-underline">
                <?php esc_html_e('Réserver', 'starter'); ?>
            </a>
        </div>

    </div>

    <div class="border-cream/20 border-t">
        <p class="wrap m-0 py-4 text-sm opacity-70">
            &copy; <?php echo esc_html(date('Y')); ?> <?php bloginfo('name'); ?>
        </p>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
