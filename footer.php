<?php
?>
<footer class="site-footer">

    <?php
    /* -------------------------------------------------
       MODALE DE CONTACT
       ------------------------------------------------- */
    // Le fichier `template-part/modal-contact.php` doit contenir
    // uniquement le markup HTML de la modale (pas de <script>).
    get_template_part( 'template-part/modal-contact' );
    ?>

    <?php
    /* -------------------------------------------------
       MENU FOOTER
       ------------------------------------------------- */
    wp_nav_menu( array(
        'theme_location'  => 'footer_menu',          // Emplacement du menu déclaré dans functions.php
        'container'       => 'nav',                  // Balise qui entoure le menu
        'container_class' => 'footer-navigation',    // Classe CSS du conteneur
        'menu_id'         => 'menu-footer-menu',     // ID du <ul> généré
        'fallback_cb'     => false,                  // Pas de menu de secours
    ) );
    ?>

</footer>

<?php
/* -------------------------------------------------
   FOOTER WORDPRESS
   -------------------------------------------------
*/
wp_footer();
?>
</body>
</html>