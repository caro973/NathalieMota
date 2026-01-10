<footer class="site-footer">
<?php get_template_part("./template-part/modal-contact");
    
    wp_nav_menu(array(
        'theme_location' => 'footer_menu', // Emplacement du menu
        'container'      => 'nav',// Balise HTML qui entoure le menu
        'container_class' => 'footer-navigation', // Classe CSS pour le conteneur
        'menu_id'        => 'menu-footer-menu', // ID du menu
        'fallback_cb'    => false, // Désactive le menu de secours
    ));
    ?>
</footer>     

<?php wp_footer(); ?>
</body>
</html>