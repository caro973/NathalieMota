<?php
/**
 * Template Name: Galerie Photos
 * Template pour afficher la galerie de photos avec filtres
 */

get_header(); 
?>

<div class="photo-gallery-page">
    <?php 
    // Afficher les filtres et la galerie
    echo do_shortcode('[filtres_dynamiques]'); 
    ?>
</div>

<?php get_footer(); ?>