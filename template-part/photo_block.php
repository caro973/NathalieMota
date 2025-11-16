<!-- Section | Filtres -->
<div class="photo-filters">
    <!-- Filtre Catégories -->
    <select name="categorie-filter" id="categorie-filter" class="choices-custom">
        <option value="">CATÉGORIES</option>
        <?php
        $categories = get_categories(array('hide_empty' => false));
        if ($categories) {
            foreach ($categories as $cat) {
                echo '<option value="' . esc_attr($cat->term_id) . '">' . esc_html($cat->name) . '</option>';
            }
        }
        ?>
    </select>

    <!-- Filtre Formats -->
    <select name="format-filter" id="format-filter" class="choices-custom">
        <option value="">FORMATS</option>
        <?php
        $formats = get_terms(array('taxonomy' => 'format', 'hide_empty' => false));
        if ($formats && !is_wp_error($formats)) {
            foreach ($formats as $format) {
                echo '<option value="' . esc_attr($format->slug) . '">' . esc_html($format->name) . '</option>';
            }
        }
        ?>
    </select>

    <!-- Filtre Tri -->
    <select name="sort-filter" id="sort-filter" class="choices-custom">
        <option value="">TRIER PAR</option>
        <option value="date_desc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'date_desc') ? 'selected' : ''; ?>>Plus récentes</option>
        <option value="date_asc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'date_asc') ? 'selected' : ''; ?>>Plus anciennes</option>
    </select>
</div>

<!-- Section | Miniatures Personnalisées -->
<div class="custom-post-thumbnails">
    <input type="hidden" name="current-page" value="1" id="current-page">
    <input type="hidden" name="current-categorie" value="" id="current-categorie">
    <input type="hidden" name="current-format" value="" id="current-format">
    <input type="hidden" name="current-sort" value="date_desc" id="current-sort">
    
    <div class="thumbnail-container-accueil">
        <?php
        // Arguments | Requête pour les publications personnalisées
        $args_custom_posts = array(
            'post_type' => 'photo',
            'posts_per_page' => 8,  // 8 photos au départ
            'orderby' => 'date',
            'order' => 'DESC',
            'paged' => 1,
        );        

        $custom_posts_query = new WP_Query($args_custom_posts);
        $max_pages = $custom_posts_query->max_num_pages;

        // Boucle | Parcourir les publications personnalisées
        while ($custom_posts_query->have_posts()) :
            $custom_posts_query->the_post();
        ?>
        <div class="custom-post-thumbnail">
            <?php if (has_post_thumbnail()) : ?>
                <div class="thumbnail-wrapper">
                    <a href="<?php the_permalink(); ?>">
                        <?php the_post_thumbnail('large'); ?>
                        <!-- Section | Overlay Catalogue -->
                        <div class="thumbnail-overlay">
                            <i class="fas fa-eye icon-eye"></i>
                            <i class="fas fa-expand-arrows-alt fullscreen-icon"></i>
                            <?php
                            // Récupère la référence et la catégorie de l'image associée.
                            $related_reference_photo = get_field('reference_photo');
                            $categories = get_the_category();
                            $category_names = array();

                            if ($categories) {
                                foreach ($categories as $category) {
                                    $category_names[] = esc_html($category->name);
                                }
                            }
                            ?>
                            <!-- Overlay | Récupère la Référence et la Catégorie -->
                            <div class="photo-info">
                                <div class="photo-info-left">
                                    <p><?php echo esc_html($related_reference_photo); ?></p>
                                </div>
                                <div class="photo-info-right">
                                    <p><?php echo implode(', ', $category_names); ?></p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endif; ?>
        </div>
        <?php endwhile; ?>

        <?php wp_reset_postdata(); ?>
    </div>
    
    <!-- Bouton Charger plus -->
    <?php if ($max_pages > 1) : ?>
    <div class="view-all-button">
        <button id="load-more-posts" data-max-pages="<?php echo esc_attr($max_pages); ?>">Charger plus</button>
    </div>
    <?php endif; ?>
</div>