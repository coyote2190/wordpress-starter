<footer class="site-footer">
    <div class="footer-widgets">
        <?php
        wp_nav_menu([
            'theme_location' => 'footer',
            'container'      => false,
            'menu_class'     => 'footer-menu',
            'fallback_cb'    => false,
        ]);
        ?>
    </div>

    <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?></p>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>