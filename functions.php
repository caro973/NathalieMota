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
        get_template_directory_uri() . '/assets/js/script.js', 
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

function adding_JQuery() {
    // Enregistrer votre script avec jQuery comme dépendance
    wp_enqueue_script(
        'adding_JQuery',
        get_template_directory_uri() . '/assets/js/jquery.js',
        array('jquery'), // Dépendance jQuery
        '1.0.0',
        true // Charger dans le footer
    );
}
add_action('wp_enqueue_scripts', 'adding_JQuery');


/**
 * SYSTÈME DE FILTRES AJAX POUR WORDPRESS
 * À ajouter dans functions.php
 */

// ============================================
// 1. ENREGISTRER LES SCRIPTS ET VARIABLES AJAX
// ============================================
function enregistrer_scripts_filtres() {
    // Créer un fichier JS inline pour s'assurer que les variables sont disponibles
    wp_add_inline_script('jquery', '
        var filtresAjax = {
            ajax_url: "' . admin_url('admin-ajax.php') . '",
            nonce: "' . wp_create_nonce('filtres_posts_nonce') . '"
        };
        console.log("Variables AJAX chargées:", filtresAjax);
    ');
}
add_action('wp_enqueue_scripts', 'enregistrer_scripts_filtres');

// ============================================
// 2. FONCTION AJAX POUR FILTRER LES POSTS
// ============================================
function filtrer_posts_ajax() {
    // Vérification de sécurité
    check_ajax_referer('filtres_posts_nonce', 'nonce');
    
    // Récupérer les paramètres
    $categories = isset($_POST['categories']) ? array_map('intval', $_POST['categories']) : array();
    $formats = isset($_POST['formats']) ? array_map('intval', $_POST['formats']) : array();
    $orderby = isset($_POST['orderby']) ? sanitize_text_field($_POST['orderby']) : 'date';
    $order = isset($_POST['order']) ? sanitize_text_field($_POST['order']) : 'DESC';
    
    // Pagination
    $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;
    $posts_per_page = 12; // Nombre de photos par page
    
    // Arguments de la requête
    $args = array(
        'post_type' => 'photo', // ⚠️ MODIFIER selon votre type de post
        'posts_per_page' => $posts_per_page,
        'paged' => $paged,
        'orderby' => $orderby,
        'order' => $order,
        'post_status' => 'publish'
    );
    
    // Ajouter les filtres de taxonomie
    $tax_query = array();
    
    if (!empty($categories)) {
        $tax_query[] = array(
            'taxonomy' => 'category', // ⚠️ MODIFIER selon votre taxonomie
            'field' => 'term_id',
            'terms' => $categories,
            'operator' => 'IN'
        );
    }
    
    if (!empty($formats)) {
        $tax_query[] = array(
            'taxonomy' => 'format', // ⚠️ MODIFIER selon votre taxonomie
            'field' => 'term_id',
            'terms' => $formats,
            'operator' => 'IN'
        );
    }
    
    if (!empty($tax_query)) {
        $args['tax_query'] = $tax_query;
        if (count($tax_query) > 1) {
            $args['tax_query']['relation'] = 'AND';
        }
    }
    
    // Exécuter la requête
    $query = new WP_Query($args);
    
    ob_start();
    
    if ($query->have_posts()) :
        echo '<div class="photos-grid">';
        while ($query->have_posts()) : $query->the_post();
            ?>
            <div class="photo-item">
                <?php if (has_post_thumbnail()) : ?>
                    <a href="<?php the_permalink(); ?>" class="photo-link">
                        <?php the_post_thumbnail('large', array('class' => 'photo-img')); ?>
                    </a>
                <?php endif; ?>
                <div class="photo-overlay">
                    <h3 class="photo-title"><?php the_title(); ?></h3>
                    <?php 
                    // Afficher la catégorie
                    $terms = get_the_terms(get_the_ID(), 'categorie');
                    if ($terms && !is_wp_error($terms)) :
                        echo '<span class="photo-category">' . esc_html($terms[0]->name) . '</span>';
                    endif;
                    ?>
                </div>
            </div>
            <?php
        endwhile;
        echo '</div>';
    else :
        echo '<div class="no-results-wrapper">';
        echo '<p class="no-results">Aucun résultat trouvé pour ces filtres.</p>';
        echo '</div>';
    endif;
    
    wp_reset_postdata();
    
    $output = ob_get_clean();
    
    wp_send_json_success(array(
        'html' => $output,
        'count' => $query->found_posts,
        'max_pages' => $query->max_num_pages,
        'current_page' => $paged
    ));
}
add_action('wp_ajax_filtrer_posts', 'filtrer_posts_ajax');
add_action('wp_ajax_nopriv_filtrer_posts', 'filtrer_posts_ajax');

// ============================================
// 3. SCRIPT JAVASCRIPT AVEC AJAX
// ============================================
function ajouter_script_filtres() {
    ?>
    <script>
    jQuery(document).ready(function($) {
        console.log('Script filtres chargé');
        console.log('filtresAjax disponible:', typeof filtresAjax !== 'undefined');
        
        var currentPage = 1;
        var maxPages = 1;
        var isLoading = false;
        
        // === OUVRIR/FERMER LES DROPDOWNS ===
        $('.filtre-header').on('click', function() {
            var dropdown = $(this).parent('.filtre-dropdown');
            var options = dropdown.find('.filtre-options');
            
            $('.filtre-dropdown').not(dropdown).removeClass('is-open');
            $('.filtre-options').not(options).slideUp(200);
            
            dropdown.toggleClass('is-open');
            options.slideToggle(200);
            
            $(this).find('.filtre-icone').toggleClass('rotate');
        });
        
        // === SÉLECTION D'UNE OPTION ===
        $('.filtre-option').on('click', function(e) {
            e.stopPropagation();
            
            var dropdown = $(this).closest('.filtre-dropdown');
            var filtreType = dropdown.data('filtre');
            
            // Pour catégories et formats : permettre multi-sélection
            if (filtreType === 'categories' || filtreType === 'formats') {
                $(this).toggleClass('selected');
                
                // Mettre à jour le label
                var selectedCount = dropdown.find('.filtre-option.selected').length;
                var labelText = dropdown.data('filtre').toUpperCase();
                
                if (selectedCount > 0) {
                    dropdown.find('.filtre-label').text(labelText + ' (' + selectedCount + ')');
                } else {
                    dropdown.find('.filtre-label').text(labelText);
                }
            } else {
                // Pour le tri : sélection unique
                var options = dropdown.find('.filtre-option');
                options.removeClass('selected');
                $(this).addClass('selected');
                
                var selectedText = $(this).text();
                dropdown.find('.filtre-label').text(selectedText);
                
                dropdown.removeClass('is-open');
                dropdown.find('.filtre-options').slideUp(200);
            }
            
            // ⚡ APPLIQUER LE FILTRE
            appliquerFiltres();
        });
        
        // === FONCTION POUR APPLIQUER LES FILTRES ===
        function appliquerFiltres(loadMore = false) {
            console.log('=== DÉBUT APPLIQUER FILTRES ===');
            
            if (isLoading) {
                console.log('⏸️ Chargement en cours, requête ignorée');
                return;
            }
            
            // Vérifier que filtresAjax existe
            if (typeof filtresAjax === 'undefined') {
                console.error('❌ filtresAjax non défini !');
                alert('Erreur: Variables AJAX non chargées. Vérifiez la console.');
                return;
            }
            
            // Reset la page si ce n'est pas "charger plus"
            if (!loadMore) {
                currentPage = 1;
            }
            
            var filtresActifs = {
                action: 'filtrer_posts',
                nonce: filtresAjax.nonce,
                categories: [],
                formats: [],
                orderby: 'date',
                order: 'DESC',
                paged: currentPage
            };
            
            // Catégories sélectionnées
            $('[data-filtre="categories"] .filtre-option.selected').each(function() {
                var termId = $(this).data('term-id');
                if (termId !== '' && termId !== undefined) {
                    filtresActifs.categories.push(termId);
                }
            });
            
            // Formats sélectionnés
            $('[data-filtre="formats"] .filtre-option.selected').each(function() {
                var termId = $(this).data('term-id');
                if (termId !== '' && termId !== undefined) {
                    filtresActifs.formats.push(termId);
                }
            });
            
            // Ordre de tri
            var triSelected = $('[data-filtre="tri"] .filtre-option.selected');
            if (triSelected.length) {
                filtresActifs.orderby = triSelected.data('orderby');
                filtresActifs.order = triSelected.data('order');
            }
            
            console.log('📊 Filtres actifs:', filtresActifs);
            console.log('🌐 URL AJAX:', filtresAjax.ajax_url);
            
            // Vérifier que le conteneur existe
            if ($('#posts-container').length === 0) {
                console.error('❌ #posts-container introuvable !');
                alert('Erreur: Le conteneur #posts-container est manquant dans votre page.');
                return;
            }
            
            // Afficher un loader
            if (!loadMore) {
                $('#posts-container').html('<div class="loader">Chargement</div>');
            } else {
                $('#load-more-btn').html('<span class="loader-btn">Chargement</span>').prop('disabled', true);
            }
            
            isLoading = true;
            
            // 🔄 APPEL AJAX
            console.log('🚀 Envoi de la requête AJAX...');
            $.ajax({
                url: filtresAjax.ajax_url,
                type: 'POST',
                data: filtresActifs,
                beforeSend: function() {
                    console.log('⏳ Requête envoyée...');
                },
                success: function(response) {
                    console.log('✅ Réponse reçue:', response);
                    if (response.success) {
                        if (loadMore) {
                            // Ajouter les nouvelles photos
                            $('.photos-grid').append($(response.data.html).find('.photo-item'));
                        } else {
                            // Remplacer tout le contenu
                            $('#posts-container').html(response.data.html);
                        }
                        
                        maxPages = response.data.max_pages;
                        console.log('📈 Nombre de résultats:', response.data.count);
                        console.log('📄 Page:', response.data.current_page, '/', maxPages);
                        
                        // Gérer le bouton "Charger plus"
                        updateLoadMoreButton();
                    } else {
                        console.error('❌ Erreur dans la réponse:', response);
                        $('#posts-container').html('<p class="no-results">Erreur lors du chargement.</p>');
                    }
                    isLoading = false;
                },
                error: function(xhr, status, error) {
                    console.error('❌ Erreur AJAX:', {xhr, status, error});
                    console.error('Response Text:', xhr.responseText);
                    $('#posts-container').html('<p class="no-results">Erreur de connexion. Vérifiez la console.</p>');
                    isLoading = false;
                }
            });
        }
        
        // === GÉRER LE BOUTON "CHARGER PLUS" ===
        function updateLoadMoreButton() {
            if ($('#load-more-container').length === 0) {
                $('#posts-container').after('<div id="load-more-container"><button id="load-more-btn" class="load-more-button">Charger plus</button></div>');
            }
            
            var btn = $('#load-more-btn');
            if (currentPage >= maxPages) {
                btn.hide();
            } else {
                btn.show().html('Charger plus').prop('disabled', false);
            }
        }
        
        // === CLIC SUR "CHARGER PLUS" ===
        $(document).on('click', '#load-more-btn', function() {
            currentPage++;
            appliquerFiltres(true);
        });
        
        // Fermer les dropdowns en cliquant ailleurs
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.filtre-dropdown').length) {
                $('.filtre-dropdown').removeClass('is-open');
                $('.filtre-options').slideUp(200);
            }
        });
        
    });
    </script>
    <?php
}
add_action('wp_footer', 'ajouter_script_filtres');

// ============================================
// 4. GÉNÉRER LES FILTRES DYNAMIQUES
// ============================================
function generer_filtres_dynamiques() {
    ob_start();
    ?>
    <div class="filtres-container">
        
        <!-- FILTRE CATÉGORIES -->
        <div class="filtre-dropdown" data-filtre="categories">
            <div class="filtre-header">
                <span class="filtre-label">CATÉGORIES</span>
                <span class="filtre-icone">▼</span>
            </div>
            <div class="filtre-options">
                <div class="filtre-option" data-term-id="">Toutes</div>
                <?php
                $categories = get_terms(array(
                    'taxonomy' => 'category', // ⚠️ MODIFIER selon votre taxonomie
                    'hide_empty' => false,
                ));
                
                if (!empty($categories) && !is_wp_error($categories)) :
                    foreach ($categories as $cat) :
                ?>
                    <div class="filtre-option" data-term-id="<?php echo esc_attr($cat->term_id); ?>">
                        <?php echo esc_html($cat->name); ?>
                    </div>
                <?php 
                    endforeach;
                endif;
                ?>
            </div>
        </div>

        <!-- FILTRE FORMATS -->
        <div class="filtre-dropdown" data-filtre="formats">
            <div class="filtre-header">
                <span class="filtre-label">FORMATS</span>
                <span class="filtre-icone">▼</span>
            </div>
            <div class="filtre-options">
                <div class="filtre-option" data-term-id="">Tous</div>
                <?php
                $formats = get_terms(array(
                    'taxonomy' => 'format',
                    'hide_empty' => false,
                ));
                
                if (!empty($formats) && !is_wp_error($formats)) :
                    foreach ($formats as $format) :
                ?>
                    <div class="filtre-option" data-term-id="<?php echo esc_attr($format->term_id); ?>">
                        <?php echo esc_html($format->name); ?>
                    </div>
                <?php 
                    endforeach;
                endif;
                ?>
            </div>
        </div>

        <!-- FILTRE TRIER PAR -->
        <div class="filtre-dropdown" data-filtre="tri">
            <div class="filtre-header">
                <span class="filtre-label">TRIER PAR</span>
                <span class="filtre-icone">▼</span>
            </div>
            <div class="filtre-options">
                <div class="filtre-option selected" data-orderby="date" data-order="DESC">Plus récent</div>
                <div class="filtre-option" data-orderby="date" data-order="ASC">Plus ancien</div>
                <div class="filtre-option" data-orderby="title" data-order="ASC">A-Z</div>
                <div class="filtre-option" data-orderby="title" data-order="DESC">Z-A</div>
            </div>
        </div>

    </div>
    
    <!-- CONTENEUR DES RÉSULTATS -->
    <div id="posts-container">
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
            
        </div>  
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('filtres_dynamiques', 'generer_filtres_dynamiques');

