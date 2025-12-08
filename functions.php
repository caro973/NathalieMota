<?php
// ============================================
// CHARGEMENT DES STYLES ET SCRIPTS
// ============================================

function nathalie_mota_enqueue_styles() {
    wp_enqueue_style(
        'nathalie-mota-style', 
        get_stylesheet_uri(), 
        array(), 
        '1.0'
    );
}
add_action('wp_enqueue_scripts', 'nathalie_mota_enqueue_styles');

function motaphoto_enqueue_fonts() {
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Space+Mono:ital,wght@0,400;0,700;1,400;1,700&family=Poppins:wght@300;400;500;600;700&display=swap');
}
add_action('wp_enqueue_scripts', 'motaphoto_enqueue_fonts');

function motaphoto_enqueue_scripts() {
    
    // jQuery (inclus dans WordPress)
    wp_enqueue_script('jquery');
    
    // Script principal
    wp_enqueue_script(
        'motaphoto-scripts',
        get_template_directory_uri() . '/assets/js/script.js',
        array('jquery'),
        '1.0.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'motaphoto_enqueue_scripts');

// ============================================
// CONFIGURATION DU THÈME
// ============================================

function mon_theme_setup() {
    // Support des menus
    register_nav_menus(array(
        'menu-principal' => 'Menu Principal',
        'footer_menu'  => 'Footer Menu',
    ));
    
    // Support des images à la une
    add_theme_support('post-thumbnails');
    
    // Tailles d'images personnalisées
    add_image_size('photo-thumbnail', 564, 495, true);
    add_image_size('photo-large', 1200, 800, false);
}
add_action('after_setup_theme', 'mon_theme_setup');

// ============================================
// ENREGISTRER LE CUSTOM POST TYPE "PHOTO"
// ============================================

function create_photo_post_type() {
    register_post_type('photo',
        array(
            'labels' => array(
                'name' => 'Photos',
                'singular_name' => 'Photo',
                'add_new' => 'Ajouter une photo',
                'add_new_item' => 'Ajouter une nouvelle photo',
                'edit_item' => 'Modifier la photo',
                'new_item' => 'Nouvelle photo',
                'view_item' => 'Voir la photo',
                'search_items' => 'Rechercher des photos',
                'not_found' => 'Aucune photo trouvée',
                'not_found_in_trash' => 'Aucune photo dans la corbeille'
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'photo'),
            'supports' => array('title', 'editor', 'thumbnail'),
            'menu_icon' => 'dashicons-camera',
            'show_in_rest' => true,
        )
    );
    
    // Taxonomie Format
    register_taxonomy(
        'format',
        'photo',
        array(
            'label' => 'Formats',
            'hierarchical' => true,
            'show_in_rest' => true,
        )
    );
}
add_action('init', 'create_photo_post_type');

// ============================================
// VARIABLES AJAX
// ============================================

function enregistrer_scripts_filtres() {
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
// FONCTION AJAX POUR FILTRER LES PHOTOS
// ============================================

function filtrer_posts_ajax() {
    check_ajax_referer('filtres_posts_nonce', 'nonce');
    
    $categories = isset($_POST['categories']) ? array_map('intval', $_POST['categories']) : array();
    $formats = isset($_POST['formats']) ? array_map('intval', $_POST['formats']) : array();
    $orderby = isset($_POST['orderby']) ? sanitize_text_field($_POST['orderby']) : 'date';
    $order = isset($_POST['order']) ? sanitize_text_field($_POST['order']) : 'DESC';
    $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;
    $posts_per_page = 8;
    
    $args = array(
        'post_type' => 'photo',
        'posts_per_page' => $posts_per_page,
        'paged' => $paged,
        'orderby' => $orderby,
        'order' => $order,
        'post_status' => 'publish'
    );
    
    $tax_query = array();
    
    if (!empty($categories)) {
        $tax_query[] = array(
            'taxonomy' => 'category',
            'field' => 'term_id',
            'terms' => $categories,
            'operator' => 'IN'
        );
    }
    
    if (!empty($formats)) {
        $tax_query[] = array(
            'taxonomy' => 'format',
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
    
    $query = new WP_Query($args);
    
    ob_start();
    
    if ($query->have_posts()) :
        echo '<div class="thumbnail-container-accueil">';
        while ($query->have_posts()) : $query->the_post();
            get_template_part_photo_item();
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
// FONCTION HELPER POUR AFFICHER UNE PHOTO
// ============================================

function get_template_part_photo_item() {
    if (has_post_thumbnail()) : ?>
        <div class="custom-post-thumbnail">
            <div class="thumbnail-wrapper">
                <a href="<?php the_permalink(); ?>">
                    <?php the_post_thumbnail('photo-thumbnail'); ?>
                    <div class="thumbnail-overlay">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/Icon_eye.png" 
                                alt="Voir" 
                                class="icon-eye" />
                    <?php
                        $reference = esc_html( get_field('references') );          // ou toute autre métadonnée
                        $categories = get_the_category();
                        $cat_names  = wp_list_pluck( $categories, 'name' );
                        $cat_string = esc_html( implode( ', ', $cat_names ) );
                        ?>
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/Icon_fullscreen.png" 
                                alt="Plein écran" 
                                class="fullscreen-icon lightbox-trigger"
                                data-full-image="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'full'); ?>" 
                       
                                    data-ref="<?php echo $reference; ?>"
                            data-category="<?php echo $cat_string; ?>" />
                        <?
                        $related_reference_photo = get_field('references');
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

// ============================================
// SCRIPT JAVASCRIPT AVEC AJAX
// ============================================

function ajouter_script_filtres() {
    ?>
    <script>
    jQuery(document).ready(function($) {
        console.log('Script filtres chargé');
        console.log('filtresAjax disponible:', typeof filtresAjax !== 'undefined');
        
        var currentPage = 1;
        var maxPages = <?php 
            // Calculer max_pages au chargement
            $initial_query = new WP_Query(array(
                'post_type' => 'photo',
                'posts_per_page' => 8
            ));
            echo $initial_query->max_num_pages;
            wp_reset_postdata();
        ?>;
        var isLoading = false;
        
        console.log('Max pages initial:', maxPages);
        
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
            
            if (filtreType === 'categories' || filtreType === 'formats') {
                $(this).toggleClass('selected');
                
                var selectedCount = dropdown.find('.filtre-option.selected').length;
                var labelText = dropdown.data('filtre').toUpperCase();
                
                if (selectedCount > 0) {
                    dropdown.find('.filtre-label').text(labelText + ' (' + selectedCount + ')');
                } else {
                    dropdown.find('.filtre-label').text(labelText);
                }
            } else {
                var options = dropdown.find('.filtre-option');
                options.removeClass('selected');
                $(this).addClass('selected');
                
                var selectedText = $(this).text();
                dropdown.find('.filtre-label').text(selectedText);
                
                dropdown.removeClass('is-open');
                dropdown.find('.filtre-options').slideUp(200);
            }
            
            appliquerFiltres();
        });
        
        // === FONCTION POUR APPLIQUER LES FILTRES ===
        function appliquerFiltres(loadMore = false) {
            console.log('=== DÉBUT APPLIQUER FILTRES ===');
            
            if (isLoading) {
                console.log('Chargement en cours, requête ignorée');
                return;
            }
            
            if (typeof filtresAjax === 'undefined') {
                console.error(' filtresAjax non défini !');
                return;
            }
            
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
            
            $('[data-filtre="categories"] .filtre-option.selected').each(function() {
                var termId = $(this).data('term-id');
                if (termId !== '' && termId !== undefined) {
                    filtresActifs.categories.push(termId);
                }
            });
            
            $('[data-filtre="formats"] .filtre-option.selected').each(function() {
                var termId = $(this).data('term-id');
                if (termId !== '' && termId !== undefined) {
                    filtresActifs.formats.push(termId);
                }
            });
            
            var triSelected = $('[data-filtre="tri"] .filtre-option.selected');
            if (triSelected.length) {
                filtresActifs.orderby = triSelected.data('orderby');
                filtresActifs.order = triSelected.data('order');
            }
            
            console.log(' Filtres actifs:', filtresActifs);
            
            if ($('#posts-container').length === 0) {
                console.error(' #posts-container introuvable !');
                return;
            }
            
            if (!loadMore) {
                $('#posts-container').html('<div class="loader">Chargement</div>');
            } else {
                $('#load-more-btn').html('<span class="loader-btn">Chargement</span>').prop('disabled', true);
            }
            
            isLoading = true;
            
            $.ajax({
                url: filtresAjax.ajax_url,
                type: 'POST',
                data: filtresActifs,
                success: function(response) {
                    console.log('Réponse reçue:', response);
                    if (response.success) {
                        if (loadMore) {
                            $('.thumbnail-container-accueil').append($(response.data.html).find('.custom-post-thumbnail'));
                        } else {
                            $('#posts-container').html(response.data.html);
                        }
                        
                        maxPages = response.data.max_pages;
                        console.log(' Nombre de résultats:', response.data.count);
                        console.log(' Page:', response.data.current_page, '/', maxPages);
                        
                        updateLoadMoreButton();
                    } else {
                        console.error(' Erreur dans la réponse:', response);
                        $('#posts-container').html('<p class="no-results">Erreur lors du chargement.</p>');
                    }
                    isLoading = false;
                },
                error: function(xhr, status, error) {
                    console.error(' Erreur AJAX:', {xhr, status, error});
                    $('#posts-container').html('<p class="no-results">Erreur de connexion.</p>');
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
                btn.html('Charger plus').prop('disabled', true).css('opacity', '0.5');
            } else {
                btn.html('Charger plus').prop('disabled', false).css('opacity', '1');
            }
            
            btn.show();
            $('#load-more-container').show();
        }
        
        // === CLIC SUR "CHARGER PLUS" ===
        $(document).on('click', '#load-more-btn', function() {
            if (!$(this).prop('disabled')) {
                currentPage++;
                appliquerFiltres(true);
            }
        });
        
        // Fermer les dropdowns en cliquant ailleurs
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.filtre-dropdown').length) {
                $('.filtre-dropdown').removeClass('is-open');
                $('.filtre-options').slideUp(200);
            }
        });
        
        // Initialiser le bouton au chargement de la page
        setTimeout(function() {
            updateLoadMoreButton();
        }, 500);

        
// === LIGHTBOX PLEIN ÉCRAN (POPUP MODALE CENTRÉE) ===
var currentIndex = 0;
var galleryItems = [];

$(document).on('click', '.lightbox-trigger', function(e) {
    e.preventDefault();
    e.stopPropagation();

    // Récupérer tous les éléments de la galerie
    galleryItems = $('.lightbox-trigger').toArray();
    currentIndex = galleryItems.indexOf(this);

    var fullImageUrl = $(this).data('full-image');
    var ref = $(this).data('ref');
    var category = $(this).data('category');

    // Créer la popup si elle n'existe pas
    if ($('#lightbox-popup').length === 0) {
        $('body').append(`
            <div id="lightbox-overlay" class="lightbox-overlay"></div>
            <div id="lightbox-popup" class="lightbox-popup">
                <button class="lightbox-close">&times;</button>
                <button class="lightbox-nav lightbox-prev">&#10094; Précédent</button>
                <div class="lightbox-content">
                    <img id="lightbox-image" src="">
                    <div class="lightbox-footer">
                        <span id="lightbox-ref">Référence: ${ref}</span>
                        <span id="lightbox-category">${category}</span>
                    </div>
                </div>
                <button class="lightbox-nav lightbox-next">Suivant &#10095;</button>
            </div>
        `);
    }

    // Mettre à jour l'image et les infos
    $('#lightbox-image').attr('src', fullImageUrl);
    $('#lightbox-ref').text('Référence: ' + ref);
    $('#lightbox-category').text(category);

    // Afficher la popup
    $('#lightbox-overlay, #lightbox-popup').fadeIn(300);
    $('body').addClass('lightbox-open');
});

// Navigation précédente
$(document).on('click', '.lightbox-prev', function(e) {
    e.stopPropagation();
    currentIndex = (currentIndex - 1 + galleryItems.length) % galleryItems.length;
    var item = $(galleryItems[currentIndex]);
    $('#lightbox-image').attr('src', item.data('full-image'));
    $('#lightbox-ref').text('Référence: ' + item.data('ref'));
    $('#lightbox-category').text(item.data('category'));
});

// Navigation suivante
$(document).on('click', '.lightbox-next', function(e) {
    e.stopPropagation();
    currentIndex = (currentIndex + 1) % galleryItems.length;
    var item = $(galleryItems[currentIndex]);
    $('#lightbox-image').attr('src', item.data('full-image'));
    $('#lightbox-ref').text('Référence: ' + item.data('ref'));
    $('#lightbox-category').text(item.data('category'));
});

// Fermeture de la popup
$(document).on('click', '.lightbox-close, #lightbox-overlay', function(e) {
    $('#lightbox-overlay, #lightbox-popup').fadeOut(300);
    $('body').removeClass('lightbox-open');
});

// Navigation au clavier
$(document).keyup(function(e) {
    if ($('#lightbox-popup').is(':visible')) {
        if (e.key === "Escape") {
            $('.lightbox-close').click();
        } else if (e.key === "ArrowLeft") {
            $('.lightbox-prev').click();
        } else if (e.key === "ArrowRight") {
            $('.lightbox-next').click();
        }
    }
});

        // === FIN DU BLOC LIGHTBOX ===
        
    }); // ← Fermeture du jQuery(document).ready
    </script>

    <?php
}
add_action('wp_footer', 'ajouter_script_filtres');

// ============================================
// GÉNÉRER LES FILTRES DYNAMIQUES
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
                    'taxonomy' => 'category',
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
            </div>
        </div>

    </div>
    
    <!-- CONTENEUR DES RÉSULTATS -->
    <div id="posts-container">
        <div class="thumbnail-container-accueil">
            <?php
            $args_custom_posts = array(
                'post_type' => 'photo',
                'posts_per_page' => 8,
                'orderby' => 'date',
                'order' => 'DESC',
                'paged' => 1,
            );        

            $custom_posts_query = new WP_Query($args_custom_posts);

            while ($custom_posts_query->have_posts()) :
                $custom_posts_query->the_post();
                get_template_part_photo_item();
            endwhile;

            wp_reset_postdata();
            ?>
        </div>
    </div>
    
    <!-- Bouton Charger Plus (initialisé au chargement) -->
    <div id="load-more-container" style="display: flex; justify-content: center; margin: 40px 0;">
        <button id="load-more-btn" class="load-more-button">Charger plus</button>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('filtres_dynamiques', 'generer_filtres_dynamiques');