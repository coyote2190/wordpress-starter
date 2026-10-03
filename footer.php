<footer class="border-line mt-16 border-t">
    <div class="wrap flex flex-col gap-4 py-8 md:flex-row md:items-center md:justify-between">

        <?php wp_nav_menu([
            'theme_location' => 'footer',
            'container' => false,
            'menu_class' => 'm-0 flex list-none flex-wrap gap-4 p-0 [&_a]:no-underline',
            'fallback_cb' => false,
        ]); ?>

        <p class="text-muted m-0 text-sm">
            &copy; <?php echo esc_html(date('Y')); ?> <?php bloginfo('name'); ?>
        </p>

    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
