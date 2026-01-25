<?php
/**
 * Template Name: Galerie Photos
 * Description: Affiche la galerie de photos avec les filtres dynamiques.
 */

get_header(); ?>

<main id="primary" class="site-main">

    <section class="photo-gallery-page">

        <?php
        // Le shortcode est transformé en HTML grâce à do_shortcode()
        echo do_shortcode( '[filtres_dynamiques]' );
        ?>

    </section>

</main><!-- #primary -->

<?php get_footer(); ?>