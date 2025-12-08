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

            <?php 
    // Afficher l'image header seulement sur la page d'accueil
    if (is_front_page()) : 
    ?>
        <img class="header" src="<?php echo get_template_directory_uri(); ?>/assets/images/Header.png" alt="image Header" />
    <?php endif; ?>
 
    
</body>
</html>