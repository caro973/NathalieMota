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
        'footer_menu'  => 'Footer Menu',
    ));
}
add_action('after_setup_theme', 'mon_theme_setup');

function mon_theme_scripts() {
    wp_enqueue_script(
        'mon-theme-scripts', // Identifiant unique
        get_template_directory_uri() . '/js/script.js', // Chemin corrigé
        array(), // Dépendances (laissez vide si aucune)
        '1.0.0', // Version
        true // Charger dans le footer
    );
}
add_action('wp_enqueue_scripts', 'mon_theme_scripts');


