<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php wp_title('|', true, 'right'); bloginfo('name'); ?></title>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    
    <nav class="menu-navigation">
        <div class="logo">
            <a href="<?php echo esc_url(home_url('/')); ?>">
                <img class="header__heading" src="<?php echo get_template_directory_uri(); ?>/assets/images/Logo.png" alt="Logo Nathalie Mota" />
            </a>
        </div>
        
        <!-- Bouton burger mobile -->
        <button class="menu-toggle" id="menu-toggle" aria-label="Menu">
            <span></span>
            <span></span>
            <span></span>
        </button>
        
        <?php
        wp_nav_menu(array(
            'theme_location' => 'menu-principal',
            'container'      => false,
            'menu_class'     => 'menu-liste',
            'fallback_cb'    => false,
        ));
        ?>
    </nav>
    
    <script>
    // Menu burger mobile
    document.addEventListener('DOMContentLoaded', function() {
        const menuToggle = document.getElementById('menu-toggle');
        const menuListe = document.querySelector('.menu-liste');
        
        if (menuToggle && menuListe) {
            menuToggle.addEventListener('click', function() {
                this.classList.toggle('active');
                menuListe.classList.toggle('mobile-menu-open');
                document.body.style.overflow = menuListe.classList.contains('mobile-menu-open') ? 'hidden' : '';
            });
            
            // Fermer le menu quand on clique sur un lien
            const menuLinks = menuListe.querySelectorAll('a');
            menuLinks.forEach(link => {
                link.addEventListener('click', function() {
                    menuToggle.classList.remove('active');
                    menuListe.classList.remove('mobile-menu-open');
                    document.body.style.overflow = '';
                });
            });
        }
    });
    </script>
    
    <?php 
    // Afficher l'image header seulement sur la page d'accueil
    if (is_front_page()) : 
    ?>
        <img class="header" src="<?php echo get_template_directory_uri(); ?>/assets/images/Header.png" alt="image Header" />
    <?php endif; ?>
    
    <!-- Modale de contact -->
    <div id="contact-modal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeContactModal()">&times;</span>
            
            <div class="logo-container">
                <img class="contact-header" src="<?php echo get_template_directory_uri(); ?>/assets/images/contact-header.png" alt="Contact" />
            </div>
            
            <?php 
            // Insérer le formulaire Contact Form 7
            // Remplacez '4' par l'ID de votre formulaire
            echo do_shortcode('[contact-form-7 id="4" title="Formulaire de contact"]'); 
            ?>
        </div>
    </div>
    
    <script>
    // Fonctions pour la modale de contact
    function openContactModal(reference) {
        document.getElementById('contact-modal').style.display = 'block';
        
        // Pré-remplir le champ référence si disponible
        if (reference) {
            setTimeout(function() {
                var refField = document.querySelector('input[name="your-reference"]');
                if (refField) {
                    refField.value = reference;
                }
            }, 100);
        }
    }
    
    function closeContactModal() {
        document.getElementById('contact-modal').style.display = 'none';
    }
    
    // Fermer la modale en cliquant en dehors
    window.onclick = function(event) {
        var modal = document.getElementById('contact-modal');
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }
    
    // Ouvrir la modale depuis le menu
    jQuery(document).ready(function($) {
        $('.menu-liste a[href*="contact"]').click(function(e) {
            e.preventDefault();
            openContactModal();
        });
    });
    </script>
</body>
</html>