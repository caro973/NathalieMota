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
        get_template_directory_uri() . '/js/script.js', 
        array(), // Dépendances (laissez vide si aucune)
        '1.0.0', // Version
        true // Charger dans le footer
    );
}
add_action('wp_enqueue_scripts', 'mon_theme_scripts');



// Enregistrer les scripts
function motaphoto_enqueue_scripts() {
    // Font Awesome
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');
    
    // Script des filtres
    wp_enqueue_script(
        'photo-filters',
        get_template_directory_uri() . '/assets/js/script.js',
        array(),
        '1.0.0',
        true
    );
    
    wp_localize_script('photo-filters', 'ajaxurl', admin_url('admin-ajax.php'));
}
add_action('wp_enqueue_scripts', 'motaphoto_enqueue_scripts');

// Fonction AJAX pour filtrer les photos
function filter_photos() {
    $categorie = isset($_POST['categorie']) ? intval($_POST['categorie']) : 0;
    $format = isset($_POST['format']) ? sanitize_text_field($_POST['format']) : '';
    $sort = isset($_POST['sort']) ? sanitize_text_field($_POST['sort']) : 'date_desc';
    $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
    
    $args = array(
        'post_type' => 'photo',
        'posts_per_page' => 8,
        'paged' => $page,
    );
    
    // Filtre par catégorie (WP Core)
    if ($categorie) {
        $args['cat'] = $categorie;
    }
    
    // Filtre par format (Taxonomie personnalisée)
    if ($format) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'format',
                'field' => 'slug',
                'terms' => $format,
            ),
        );
    }
    
    // Tri
    if ($sort === 'date_asc') {
        $args['orderby'] = 'date';
        $args['order'] = 'ASC';
    } else {
        $args['orderby'] = 'date';
        $args['order'] = 'DESC';
    }
    
    $query = new WP_Query($args);
    
    ob_start();
    
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            get_template_part_photo_item(); // Fonction helper ci-dessous
        }
    } else {
        echo '<p class="no-photos">Aucune photo trouvée.</p>';
    }
    
    $html = ob_get_clean();
    wp_reset_postdata();
    
    wp_send_json_success(array(
        'html' => $html,
        'max_pages' => $query->max_num_pages
    ));
}
add_action('wp_ajax_filter_photos', 'filter_photos');
add_action('wp_ajax_nopriv_filter_photos', 'filter_photos');

// Fonction AJAX pour charger plus de photos
function load_more_photos() {
    $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $categorie = isset($_POST['categorie']) ? intval($_POST['categorie']) : 0;
    $format = isset($_POST['format']) ? sanitize_text_field($_POST['format']) : '';
    $sort = isset($_POST['sort']) ? sanitize_text_field($_POST['sort']) : 'date_desc';
    
    $args = array(
        'post_type' => 'photo',
        'posts_per_page' => 8,
        'paged' => $page,
    );
    
    // Filtre par catégorie
    if ($categorie) {
        $args['cat'] = $categorie;
    }
    
    // Filtre par format
    if ($format) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'format',
                'field' => 'slug',
                'terms' => $format,
            ),
        );
    }
    
    // Tri
    if ($sort === 'date_asc') {
        $args['orderby'] = 'date';
        $args['order'] = 'ASC';
    } else {
        $args['orderby'] = 'date';
        $args['order'] = 'DESC';
    }
    
    $query = new WP_Query($args);
    
    ob_start();
    
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            get_template_part_photo_item(); // Fonction helper ci-dessous
        }
    }
    
    $html = ob_get_clean();
    wp_reset_postdata();
    
    wp_send_json_success(array(
        'html' => $html,
        'max_pages' => $query->max_num_pages
    ));
}
add_action('wp_ajax_load_more_photos', 'load_more_photos');
add_action('wp_ajax_nopriv_load_more_photos', 'load_more_photos');

// Fonction helper pour afficher une photo (évite la duplication de code)
function get_template_part_photo_item() {
    if (has_post_thumbnail()) : ?>
        <div class="custom-post-thumbnail">
            <div class="thumbnail-wrapper">
                <a href="<?php the_permalink(); ?>">
                    <?php the_post_thumbnail('large'); ?>
                    <div class="thumbnail-overlay">
                        <i class="fas fa-eye icon-eye"></i>
                        <i class="fas fa-expand-arrows-alt fullscreen-icon"></i>
                        <?php
                        $related_reference_photo = get_field('reference_photo');
                        $categories = get_the_category();
                        $category_names = array();
                        if ($categories) {
                            foreach ($categories as $category) {
                                $category_names[] = esc_html($category->name);
                            }
                        }
                        ?>
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
        </div>
    <?php endif;
}

function motaphoto_enqueue_fonts() {
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Space+Mono:ital,wght@0,400;0,700;1,400;1,700&family=Poppins:wght@300;400;500;600&display=swap');
}
add_action('wp_enqueue_scripts', 'motaphoto_enqueue_fonts');