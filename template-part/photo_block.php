<?php
/**
 * Template Name: Galerie Photos
 * Description: Affiche la galerie de photos avec les filtres dynamiques.
 */

get_header(); ?>

<main id="primary" class="site-main">

    <section class="photo-gallery-page">

        <?php
        echo do_shortcode( '[filtres_dynamiques]' );
        ?>

    </section>

</main>

<?php get_footer(); ?>