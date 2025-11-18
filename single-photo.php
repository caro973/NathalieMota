<?php
/**
 * Single Photo Template
 * Ce fichier doit être nommé : single-photo.php
 */

get_header(); 
?>

<div class="single-photo-page">
    <?php while (have_posts()) : the_post(); ?>
        
        <div class="single-photo-container">
            <!-- COLONNE GAUCHE : Informations -->
            <div class="single-photo-info">
                <h1 class="photo-title"><?php the_title(); ?></h1>
                
                <div class="photo-details">
                    <?php 
                    // Récupérer les champs ACF
                    $reference = get_field('reference_photo');
                    $type = get_field('type');
                    
                    // Récupérer les catégories
                    $categories = get_the_category();
                    $category_names = array();
                    if ($categories) {
                        foreach ($categories as $category) {
                            $category_names[] = esc_html($category->name);
                        }
                    }
                    
                    // Récupérer les formats (taxonomie personnalisée)
                    $formats = get_the_terms(get_the_ID(), 'format');
                    $format_names = array();
                    if ($formats && !is_wp_error($formats)) {
                        foreach ($formats as $format) {
                            $format_names[] = esc_html($format->name);
                        }
                    }
                    
                    // Récupérer l'année
                    $annee = get_the_date('Y');
                    ?>
                    
                    <?php if ($reference) : ?>
                    <div class="photo-detail-item">
                        <span class="detail-label">Référence :</span>
                        <span class="detail-value"><?php echo esc_html($reference); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($category_names)) : ?>
                    <div class="photo-detail-item">
                        <span class="detail-label">Catégorie :</span>
                        <span class="detail-value"><?php echo implode(', ', $category_names); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($format_names)) : ?>
                    <div class="photo-detail-item">
                        <span class="detail-label">Format :</span>
                        <span class="detail-value"><?php echo implode(', ', $format_names); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($type) : ?>
                    <div class="photo-detail-item">
                        <span class="detail-label">Type :</span>
                        <span class="detail-value"><?php echo esc_html($type); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="photo-detail-item">
                        <span class="detail-label">Année :</span>
                        <span class="detail-value"><?php echo esc_html($annee); ?></span>
                    </div>
                </div>
                
                <!-- Section Contact -->
                <div class="single-photo-contact">
                    <p class="contact-text">Cette photo vous intéresse ?</p>
                    <button class="contact-button" onclick="openContactModal('<?php echo esc_js($reference); ?>')">
                        Contact
                    </button>
                </div>
            </div>
            
            <!-- COLONNE DROITE : Image -->
            <div class="single-photo-image">
                <?php if (has_post_thumbnail()) : ?>
                    <?php the_post_thumbnail('full'); ?>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Navigation entre photos -->
        <div class="single-photo-navigation">
            <div class="photo-nav-thumbnail">
                <?php
                // Photo suivante
                $next_post = get_next_post();
                if (!empty($next_post)) {
                    echo get_the_post_thumbnail($next_post->ID, 'thumbnail');
                }
                ?>
            </div>
            
            <div class="photo-nav-arrows">
                <?php
                $prev_post = get_previous_post();
                $next_post = get_next_post();
                ?>
                
                <button class="nav-arrow nav-prev" 
                        <?php if (empty($prev_post)) echo 'disabled'; ?>
                        onclick="window.location.href='<?php echo !empty($prev_post) ? get_permalink($prev_post->ID) : '#'; ?>'">
                    ←
                </button>
                
                <button class="nav-arrow nav-next" 
                        <?php if (empty($next_post)) echo 'disabled'; ?>
                        onclick="window.location.href='<?php echo !empty($next_post) ? get_permalink($next_post->ID) : '#'; ?>'">
                    →
                </button>
            </div>
        </div>
        
        <!-- Section "Vous aimerez aussi" -->
        <div class="related-photos">
            <h2>Vous aimerez aussi</h2>
            
            <div class="related-photos-grid">
                <?php
                // Récupérer 2 photos aléatoires de la même catégorie
                $current_categories = wp_get_post_categories(get_the_ID());
                
                $related_args = array(
                    'post_type' => 'photo',
                    'posts_per_page' => 2,
                    'post__not_in' => array(get_the_ID()),
                    'orderby' => 'rand',
                );
                
                if (!empty($current_categories)) {
                    $related_args['category__in'] = $current_categories;
                }
                
                $related_query = new WP_Query($related_args);
                
                if ($related_query->have_posts()) :
                    while ($related_query->have_posts()) : $related_query->the_post();
                        ?>
                        <div class="custom-post-thumbnail">
                            <div class="thumbnail-wrapper">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('photo-thumbnail'); ?>
                                    <div class="thumbnail-overlay">
                                        <i class="fas fa-eye icon-eye"></i>
                                        <i class="fas fa-expand-arrows-alt fullscreen-icon"></i>
                                        <?php
                                        $related_reference = get_field('reference_photo');
                                        $related_categories = get_the_category();
                                        $related_cat_names = array();
                                        if ($related_categories) {
                                            foreach ($related_categories as $cat) {
                                                $related_cat_names[] = esc_html($cat->name);
                                            }
                                        }
                                        ?>
                                        <div class="photo-info">
                                            <div class="photo-info-left">
                                                <p><?php echo esc_html($related_reference); ?></p>
                                            </div>
                                            <div class="photo-info-right">
                                                <p><?php echo implode(', ', $related_cat_names); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                endif;
                ?>
            </div>
        </div>
        
    <?php endwhile; ?>
</div>

<script>
// Fonction pour ouvrir la modale de contact avec la référence pré-remplie
function openContactModal(reference) {
    // Si vous avez une modale de contact, l'ouvrir ici
    // et pré-remplir le champ référence
    console.log('Ouvrir modale contact avec référence:', reference);
    
    // Exemple si vous avez une modale jQuery
    if (typeof jQuery !== 'undefined') {
        jQuery('#contact-modal').fadeIn();
        jQuery('#reference-field').val(reference);
    }
}

// Prévisualisation de la photo au survol de la miniature
jQuery(document).ready(function($) {
    var previewTimeout;
    
    $('.photo-nav-arrows button').hover(
        function() {
            var $thumbnail = $('.photo-nav-thumbnail');
            var direction = $(this).hasClass('nav-prev') ? 'prev' : 'next';
            
            previewTimeout = setTimeout(function() {
                $thumbnail.addClass('show-preview');
            }, 500);
        },
        function() {
            clearTimeout(previewTimeout);
            $('.photo-nav-thumbnail').removeClass('show-preview');
        }
    );
});
</script>

<?php get_footer(); ?>