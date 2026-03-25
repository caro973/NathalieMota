<?php
/**
 * Template part – Affichage d’une vignette du CPT « photo ».
 * Fichier dans : wp-content/themes/votre-theme/template-part/content-photo.php
 */

if ( ! has_post_thumbnail() ) {
    // Pas d’image → on ne renvoie rien.
    return;
}
?>
<div class="custom-post-thumbnail">
    <div class="thumbnail-wrapper">
        <a href="<?php the_permalink(); ?>">
            <?php the_post_thumbnail( 'photo-thumbnail' ); ?>

            <div class="thumbnail-overlay">
                <!-- Icône œil -->
                <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/Icon_eye.png' ); ?>"
                     alt="Voir"
                     class="icon-eye" />

                <?php
                // ------- RÉFÉRENCE (ACF ou meta) -------
                $reference = function_exists( 'get_field' )
                    ? get_field( 'references' )
                    : get_post_meta( get_the_ID(), 'references', true );

                $reference = is_string( $reference ) ? esc_html( $reference ) : '';

                // ------- CATÉGORIES (taxonomy native) -------
                $categories = get_the_category();
                $cat_names  = wp_list_pluck( $categories, 'name' );
                $cat_string = esc_html( implode( ', ', $cat_names ) );
                ?>

                <!-- Icône plein‑écran (lightbox) -->
                <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/Icon_fullscreen.png' ); ?>"
                     alt="Plein écran"
                     class="fullscreen-icon lightbox-trigger"
                     data-full-image="<?php echo esc_url( get_the_post_thumbnail_url( get_the_ID(), 'full' ) ); ?>"
                     data-ref="<?php echo $reference; ?>"
                     data-category="<?php echo $cat_string; ?>" />

                <!-- Infos affichées dans la lightbox -->
                <div class="photo-info">
                    <div class="photo-info-left"><p><?php echo $reference; ?></p></div>
                    <div class="photo-info-right"><p><?php echo $cat_string; ?></p></div>
                </div>
            </div> <!-- /.thumbnail-overlay -->
        </a>
    </div> <!-- /.thumbnail-wrapper -->
</div> <!-- /.custom-post-thumbnail -->