<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nathalie Mota</title>
    <?php wp_head() ?>
</head>
<body>
    <nav class="menu-navigation">
        <div class="logo">
            <a href="<?php echo esc_url(home_url('/')); ?>"><img class="header__heading" src="<?php echo get_template_directory_uri(); ?>/assets/images/Logo.png" alt="Logo Nathalie Mota" /></a>
        </div>
        <?php
        wp_nav_menu(array(
            'theme_location' => 'menu-principal', // Doit correspondre à l'identifiant enregistré
            'container'      => false,            // Pas de conteneur HTML autour
            'menu_class'     => 'menu-liste',     // Classe CSS pour la liste <ul>
            'fallback_cb'    => false,            // Pas de menu de secours
        ));
        ?>
    </nav>
    <img class="header" src="<?php echo get_template_directory_uri(); ?>/assets/images/Header.png" alt="image Header" />
    <?php wp_enqueue_script('jquery'); ?>
    <?php wp_enqueue_script('enregistrer_scripts_filtres'); ?>
</body>
</html>