<?php get_template_part('template_part/modale.php');?>
<footer class="footer">
   <nav class="footer__nav">
    <?php
    // Vérifie que le menu « footer_menu » a bien été assigné
    if ( has_nav_menu( 'footer_menu' ) ) {
        wp_nav_menu( array(
            'theme_location' => 'footer_menu',
            'container'      => false,
            'menu_class'     => 'footer-menu',
        ) );
    } else {
        // Optionnel : afficher un message de secours pendant le dev
        echo '<!-- Footer menu not assigned -->';
    }
    ?>
</nav>
<?php wp_footer()?>
</body>
</html>