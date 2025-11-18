<footer class="site-footer">
    <?php
    wp_nav_menu(array(
        'theme_location' => 'footer_menu',
        'container'      => 'nav',
        'container_class' => 'footer-navigation',
        'menu_id'        => 'menu-footer-menu',
        'fallback_cb'    => false,
    ));
    ?>
</footer>

<?php wp_footer(); ?>
</body>
</html>