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
    
    <!-- Modale de contact -->
    <div id="contact-modal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeContactModal()">&times;</span>
            
            <div class="logo-container">
                <div class="contact-header-text">CONTACT</div>
            </div>
            
            <?php 
            // Insérer le formulaire Contact Form 7
            // Remplacez '4' par l'ID de votre formulaire
            echo do_shortcode('[contact-form-7 id="4" title="Formulaire de contact"]'); 
            ?>
        </div>
    </div>

<?php wp_footer(); ?>
</body>
</html>