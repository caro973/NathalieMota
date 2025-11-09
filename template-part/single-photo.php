<?php
/**
 * Template pour afficher une photo individuelle
 */
get_header(); ?>

<main class="single-photo-page">
    <?php while (have_posts()) : the_post(); ?>
        
        <div class="single-photo-container">
            <!-- Image principale -->
            <div class="single-photo-image">
                <?php if (has_post_thumbnail()) : ?>
                    <?php the_post_thumbnail('full'); ?>
                <?php endif; ?>
            </div>

            <!-- Informations de la photo -->
            <div class="single-photo-info">
                <h1 class="photo-title"><?php the_title(); ?></h1>

                <div class="photo-details">
                    <?php 
                    $reference = get_field('reference_photo');
                    $categories = get_the_category();
                    $formats = get_the_terms(get_the_ID(), 'format');
                    $type = get_field('type');
                    $annee = get_the_date('Y');
                    ?>

                    <?php if ($reference) : ?>
                        <div class="photo-detail-item">
                            <span class="detail-label">RÉFÉRENCE :</span>
                            <span class="detail-value"><?php echo esc_html($reference); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if ($categories) : ?>
                        <div class="photo-detail-item">
                            <span class="detail-label">CATÉGORIE :</span>
                            <span class="detail-value"><?php echo esc_html($categories[0]->name); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if ($formats && !is_wp_error($formats)) : ?>
                        <div class="photo-detail-item">
                            <span class="detail-label">FORMAT :</span>
                            <span class="detail-value"><?php echo esc_html($formats[0]->name); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if ($type) : ?>
                        <div class="photo-detail-item">
                            <span class="detail-label">TYPE :</span>
                            <span class="detail-value"><?php echo esc_html($type); ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="photo-detail-item">
                        <span class="detail-label">ANNÉE :</span>
                        <span class="detail-value"><?php echo esc_html($annee); ?></span>
                    </div>
                </div>
            </div>

            <!-- Call to action -->
            <div class="photo-cta">
                <p class="cta-text">Cette photo vous intéresse ?</p>
                <button class="cta-button" id="contact-photo-btn">Contact</button>
            </div>
        </div>

        <!-- Section "Vous aimerez aussi" -->
        <section class="related-photos">
            <h2 class="section-title">VOUS AIMEREZ AUSSI</h2>
            
            <div class="related-photos-grid">
                <?php
                // Récupérer la catégorie de la photo actuelle
                $current_categories = get_the_category();
                $category_id = $current_categories ? $current_categories[0]->term_id : 0;
                
                // Query pour les photos similaires
                $related_args = array(
                    'post_type' => 'photo',
                    'posts_per_page' => 2,
                    'post__not_in' => array(get_the_ID()),
                    'cat' => $category_id,
                    'orderby' => 'rand'
                );
                
                $related_query = new WP_Query($related_args);
                
                if ($related_query->have_posts()) :
                    while ($related_query->have_posts()) : $related_query->the_post();
                ?>
                    <div class="related-photo-item">
                        <a href="<?php the_permalink(); ?>">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('large'); ?>
                            <?php endif; ?>
                        </a>
                    </div>
                <?php 
                    endwhile;
                    wp_reset_postdata();
                endif;
                ?>
            </div>
        </section>

    <?php endwhile; ?>
</main>

<?php get_footer(); ?>