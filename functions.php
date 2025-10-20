<?php
function nathalie_mota_enqueue_styles() {
    // Charge le style.css principal
    wp_enqueue_style(
        'nathalie-mota-style', 
        get_stylesheet_uri(), 
        array(), 
        '1.0'
    );
}
add_action('wp_enqueue_scripts', 'nathalie_mota_enqueue_styles');

function mon_theme_setup() {
    register_nav_menus(array(
        'menu-principal' => 'Menu Principal', // 'menu-principal' est l'identifiant de l'emplacement
    ));
}
add_action('after_setup_theme', 'mon_theme_setup');
