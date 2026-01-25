<?php
/* ------------------------------------------------------------------
   functions.php –  Enregistrement des assets, du CPT, du shortcode
   et du handler AJAX.  Tout est commenté ligne‑par‑ligne pour un
   développeur débutant.
   ------------------------------------------------------------------ */

/* ================================================================
   1️⃣  ENREGISTREMENT DES MENUS
   ================================================================ */
function motaphoto_register_menus() {
    register_nav_menus( array(
        // Emplacement du menu principal (généralement dans le header)
        'menu-principal' => __( 'Menu Principal', 'motaphoto' ),
        // Emplacement du menu du footer
        'footer_menu'    => __( 'Footer Menu',    'motaphoto' ),
    ) );
}
add_action( 'after_setup_theme', 'motaphoto_register_menus' );


/* ================================================================
   2️⃣  ENQUEUE DES STYLES & SCRIPTS
   ================================================================ */

function motaphoto_enqueue_assets() {

    /* ---------- STYLE PRINCIPAL (optionnel, si vous le gardez) ---------- */
    wp_enqueue_style(
        'nathalie-mota-style',
        get_stylesheet_uri(),
        array(),
        '1.0'
    );

    wp_enqueue_style(
        'nathalie-mota-main-style',
        get_stylesheet_directory_uri() . '/assets/css/style.css', // Chemin vers votre fichier CSS principal
        '1.0', // Version (peut être dynamique avec filemtime() si besoin)
        'all'  // Media (all, screen, print, etc.)
    );

    // Si vous avez un fichier CSS spécifique pour les écrans mobiles (optionnel)
    wp_enqueue_style(
        'nathalie-mota-mobile-style',
        get_stylesheet_directory_uri() . '/assets/css/mobile.css', // Chemin vers votre fichier CSS mobile
        array('nathalie-mota-main-style'), // Dépendance : charge après le CSS principal
        '1.0',
        '(max-width: 768px)' // Media query pour cibler uniquement les mobiles
    );

    // Si vous avez un fichier CSS spécifique pour les écrans desktop (optionnel)
    wp_enqueue_style(
        'nathalie-mota-desktop-style',
        get_stylesheet_directory_uri() . '/assets/css/desktop.css', // Chemin vers votre fichier CSS desktop
        array('nathalie-mota-main-style'), // Dépendance : charge après le CSS principal
        '1.0',
        '(min-width: 769px)' // Media query pour cibler uniquement les desktop/tablettes
    );

    /* ---------- GOOGLE FONTS ---------- */
    wp_enqueue_style(
        'google-fonts',
        'https://fonts.googleapis.com/css2?family=Space+Mono:ital,wght@0,400;0,700;1,400;1,700&family=Poppins:wght@300;400;500;600;700&display=swap'
    );

    /* ---------- JQUERY ---------- */
    wp_enqueue_script( 'jquery' );

    /* ---------- FILTERS + LIGHTBOX (AJAX) ---------- */
    wp_enqueue_script(
        'motaphoto-filters',
        get_template_directory_uri() . '/assets/js/filters.js',
        array( 'jquery' ),
        '1.0.0',
        true
    );

    /* ---------- SCRIPT PRINCIPAL (ES-module) ---------- */
    wp_enqueue_script(
        'motaphoto-main',
        get_template_directory_uri() . '/assets/js/main.js',
        array( 'jquery' ),
        '1.0.0',
        true
    );

    /* Script qui contient openContactModal / closeContactModal */
    wp_enqueue_script(
        'motaphoto-modal-contact',
        get_template_directory_uri() . '/assets/js/modal-contact.js',
        array( 'jquery' ),
        '1.0.0',
        true
    );

    /* ---------- AJOUT DE L'ATTRIBUT type="module" ---------- */
    add_filter( 'script_loader_tag', 'motaphoto_add_module_attribute', 10, 2 );

    /* ---------- VARIABLES AJAX ---------- */
    $initial_query = new WP_Query( array(
        'post_type'      => 'photo',
        'posts_per_page' => 8,
        'post_status'    => 'publish',
    ) );

    wp_localize_script(
        'motaphoto-filters',
        'filtresAjax',
        array(
            'ajax_url'          => admin_url( 'admin-ajax.php' ),
            'nonce'             => wp_create_nonce( 'filtres_posts_nonce' ),
            'initial_max_pages' => $initial_query->max_num_pages,
        )
    );

    wp_reset_postdata();
}

add_action( 'wp_enqueue_scripts', 'motaphoto_enqueue_assets' );

/**
 * Ajoute l’attribut `type="module"` au script principal.
 */
function motaphoto_add_module_attribute( $tag, $handle ) {
    if ( 'motaphoto-main' !== $handle ) {
        return $tag;
    }
    $src = esc_url( get_template_directory_uri() . '/assets/js/main.js' );
    return '<script type="module" src="' . $src . '"></script>';
}


/* ================================================================
   3️⃣  CUSTOM POST TYPE « photo »
   ================================================================ */
function create_photo_post_type() {
    register_post_type( 'photo', array(
        'labels' => array(
            'name'               => __( 'Photos', 'motaphoto' ),
            'singular_name'      => __( 'Photo', 'motaphoto' ),
            'add_new'            => __( 'Ajouter une photo', 'motaphoto' ),
            'add_new_item'       => __( 'Ajouter une nouvelle photo', 'motaphoto' ),
            'edit_item'          => __( 'Modifier la photo', 'motaphoto' ),
            'new_item'           => __( 'Nouvelle photo', 'motaphoto' ),
            'view_item'          => __( 'Voir la photo', 'motaphoto' ),
            'search_items'       => __( 'Rechercher des photos', 'motaphoto' ),
            'not_found'          => __( 'Aucune photo trouvée', 'motaphoto' ),
            'not_found_in_trash' => __( 'Aucune photo dans la corbeille', 'motaphoto' ),
        ),
        'public'       => true,
        'has_archive'  => true,
        'rewrite'      => array( 'slug' => 'photo' ),
        'supports'     => array( 'title', 'editor', 'thumbnail' ),
        'menu_icon'    => 'dashicons-camera',
        'show_in_rest' => true, // rend le CPT compatible Gutenberg / API REST
    ) );

    // Taxonomie personnalisée « format » (ex. portrait, paysage, macro…)
    register_taxonomy(
        'format',
        'photo',
        array(
            'label'        => __( 'Formats', 'motaphoto' ),
            'hierarchical' => true, // fonctionne comme les catégories
            'show_in_rest' => true,
        )
    );
}
add_action( 'init', 'create_photo_post_type' );


/* ================================================================
   4️⃣  SHORTCODE « filtres_dynamiques »
   ================================================================ */
function generer_filtres_dynamiques() {
    // Démarrer la capture de sortie (buffer) pour retourner du HTML.
    ob_start();
    ?>
    <!-- ====================== BLOC FILTRES ====================== -->
    <div class="filtres-container">

        <div class="filtres-cate-form">

            <!-- ==== FILTRE CATÉGORIES ==== -->
            <div class="filtre-dropdown" data-filtre="categories">
                <div class="filtre-header">
                    <span class="filtre-label">CATÉGORIES</span>
                    <span class="filtre-icone">▼</span>
                </div>
                <div class="filtre-options">
                    <div class="filtre-option" data-term-id="">Toutes</div>
                    <?php
                    $cats = get_terms( array(
                        'taxonomy'   => 'category',
                        'hide_empty' => false,
                    ) );
                    if ( $cats && ! is_wp_error( $cats ) ) :
                        foreach ( $cats as $cat ) :
                    ?>
                        <div class="filtre-option" data-term-id="<?php echo esc_attr( $cat->term_id ); ?>">
                            <?php echo esc_html( $cat->name ); ?>
                        </div>
                    <?php
                        endforeach;
                    endif;
                    ?>
                </div>
            </div>

            <!-- ==== FILTRE FORMATS ==== -->
            <div class="filtre-dropdown" data-filtre="formats">
                <div class="filtre-header">
                    <span class="filtre-label">FORMATS</span>
                    <span class="filtre-icone">▼</span>
                </div>
                <div class="filtre-options">
                    <div class="filtre-option" data-term-id="">Tous</div>
                    <?php
                    $fmts = get_terms( array(
                        'taxonomy'   => 'format',
                        'hide_empty' => false,
                    ) );
                    if ( $fmts && ! is_wp_error( $fmts ) ) :
                        foreach ( $fmts as $fmt ) :
                    ?>
                        <div class="filtre-option" data-term-id="<?php echo esc_attr( $fmt->term_id ); ?>">
                            <?php echo esc_html( $fmt->name ); ?>
                        </div>
                    <?php
                        endforeach;
                    endif;
                    ?>
                </div>
            </div>
        
        </div>

        <div class="filtre-container-trie">

            <!-- ==== TRI (ORDRE D'AFFICHAGE) ==== -->
            <div class="filtre-dropdown" data-filtre="tri">
                <div class="filtre-header">
                    <span class="filtre-label">TRIER PAR</span>
                    <span class="filtre-icone">▼</span>
                </div>
                <div class="filtre-options">
                    <div class="filtre-option selected" data-orderby="date" data-order="DESC">
                        Plus récent
                    </div>
                    <div class="filtre-option" data-orderby="date" data-order="ASC">
                        Plus ancien
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- ====================== GALERIE (RÉSULTATS) ====================== -->
    <div id="posts-container">
        <div class="thumbnail-container-accueil">
            <?php
            // Requête initiale : on charge les 8 premières photos.
            $initial = new WP_Query( array(
                'post_type'      => 'photo',
                'posts_per_page' => 8,
                'orderby'        => 'date',
                'order'          => 'DESC',
                'post_status'    => 'publish',
            ) );

            if ( $initial->have_posts() ) :
                while ( $initial->have_posts() ) : $initial->the_post();
                    // Le template‑part qui affiche chaque vignette.
                    // Fichier attendu : template-part/content-photo.php
                    get_template_part( 'template-part/content', 'photo' );
                endwhile;
            endif;
            wp_reset_postdata();
            ?>
        </div>
    </div>

    <!-- ====================== BOUTON CHARGER PLUS ====================== -->
    <div id="load-more-container" style="display:flex;justify-content:center;margin:40px 0;">
        <button id="load-more-btn" class="load-more-button">Charger plus</button>
    </div>
    <?php
    // Retourner le HTML capturé.
    return ob_get_clean();
}
add_shortcode( 'filtres_dynamiques', 'generer_filtres_dynamiques' );


/* ================================================================
   5️⃣  AJAX HANDLER – filtrer_posts
   ================================================================ */
function filtrer_posts_ajax() {
    // Vérifier le nonce (sécurité contre les requêtes CSRF)
    check_ajax_referer( 'filtres_posts_nonce', 'nonce' );

    // Récupérer les paramètres envoyés par le script JS.
    $categories = isset( $_POST['categories'] ) ? array_map( 'intval', $_POST['categories'] ) : array();
    $formats    = isset( $_POST['formats'] )    ? array_map( 'intval', $_POST['formats'] )    : array();
    $orderby    = isset( $_POST['orderby'] ) ? sanitize_text_field( $_POST['orderby'] ) : 'date';
    $order      = isset( $_POST['order'] )   ? sanitize_text_field( $_POST['order'] )   : 'DESC';
    $paged      = isset( $_POST['paged'] )   ? intval( $_POST['paged'] )                : 1;

    // Construction de la requête WP_Query.
    $args = array(
        'post_type'      => 'photo',
        'posts_per_page' => 8,
        'paged'          => $paged,
        'orderby'        => $orderby,
        'order'          => $order,
        'post_status'    => 'publish',
    );

    $tax_query = array();

    // Filtrer par catégorie (taxonomy native "category").
    if ( $categories ) {
        $tax_query[] = array(
            'taxonomy' => 'category',
            'field'    => 'term_id',
            'terms'    => $categories,
            'operator' => 'IN',
        );
    }

    // Filtrer par format (taxonomie personnalisée "format").
    if ( $formats ) {
        $tax_query[] = array(
            'taxonomy' => 'format',
            'field'    => 'term_id',
            'terms'    => $formats,
            'operator' => 'IN',
        );
    }

    // Si on a au moins un filtre taxonomique, on l’ajoute à la requête.
    if ( $tax_query ) {
        $args['tax_query'] = $tax_query;
        // Si on a deux filtres (catégorie + format) on veut que les deux soient vrais.
        if ( count( $tax_query ) > 1 ) {
            $args['tax_query']['relation'] = 'AND';
        }
    }

    // Exécuter la requête.
    $query = new WP_Query( $args );

    // Capturer le HTML généré.
    ob_start();

    if ( $query->have_posts() ) :
        echo '<div class="thumbnail-container-accueil">';
        while ( $query->have_posts() ) : $query->the_post();
            // Même template‑part que celui utilisé dans le shortcode.
            get_template_part( 'template-part/content', 'photo' );
        endwhile;
        echo '</div>';
    else :
        // Aucun résultat pour les filtres sélectionnés.
        echo '<div class="no-results-wrapper"><p class="no-results">Aucun résultat trouvé pour ces filtres.</p></div>';
    endif;

    wp_reset_postdata();

    $output = ob_get_clean();

    // Répondre au format JSON attendu par le script JS.
    wp_send_json_success( array(
        'html'         => $output,               // bloc HTML à injecter
        'count'        => $query->found_posts,   // nombre total de photos correspondant
        'max_pages'    => $query->max_num_pages, // nombre total de pages (pour le bouton « Charger plus »)
        'current_page' => $paged,                // page actuellement affichée
    ) );
}

/* Hook AJAX pour les utilisateurs connectés */
add_action( 'wp_ajax_filtrer_posts', 'filtrer_posts_ajax' );
/* Hook AJAX pour les visiteurs non connectés */
add_action( 'wp_ajax_nopriv_filtrer_posts', 'filtrer_posts_ajax' );