/*=====================================================================
  SCRIPT PRINCIPAL – GESTION DES FILTRES, PAGINATION, LIGHTBOX
  =====================================================================*/

jQuery(document).ready(function ($) {

    /* -------------------------------------------------
         Variables globales du script
          - maxPages : nombre total de pages (fourni par PHP via wp_localize_script)
          - currentPage : page courante affichée
          - isLoading : drapeau qui empêche les requêtes simultanées
       ------------------------------------------------- */
    var maxPages    = filtresAjax.initial_max_pages; // ← transmis depuis PHP
    var currentPage = 1;
    var isLoading   = false;

    /* ==============================================================
       SECTION A – GESTION DES DROPDOWNS (ouvert/fermé)
       ============================================================== */
    $('.filtre-header').on('click', function () {
        var dropdown = $(this).parent('.filtre-dropdown'); // le conteneur du filtre
        var options  = dropdown.find('.filtre-options');   // la liste d’options

        // Fermer tous les autres dropdowns
        $('.filtre-dropdown').not(dropdown).removeClass('is-open');
        $('.filtre-options').not(options).slideUp(200);

        // Ouvrir / refermer le dropdown cliqué
        dropdown.toggleClass('is-open');
        options.slideToggle(200);

        // Faire pivoter l’icône (flèche) pour indiquer l’état
        $(this).find('.filtre-icone').toggleClass('rotate');
    });

    /* ==============================================================
       SECTION B – SÉLECTION D’UNE OPTION DE FILTRE
       ============================================================== */
    $('.filtre-option').on('click', function (e) {
        e.stopPropagation(); // empêche le clic de remonter à .filtre-dropdown

        var dropdown   = $(this).closest('.filtre-dropdown');
        var filtreType = dropdown.data('filtre'); // "categories", "formats" ou "tri"

        /* -------------------------------------------------
           Cas 1 : filtres à choix multiples (catégories / formats)
           ------------------------------------------------- */
        if (filtreType === 'categories' || filtreType === 'formats') {
            $(this).toggleClass('selected'); // cocher / décocher

            // Mettre à jour le libellé du filtre (ex. « CATEGORIES (2) »)
            var selectedCount = dropdown.find('.filtre-option.selected').length;
            var labelText     = dropdown.data('filtre').toUpperCase();

            dropdown.find('.filtre-label')
                    .text(selectedCount > 0 ?
                          `${labelText} (${selectedCount})` :
                          labelText);
        }
        /* -------------------------------------------------
           Cas 2 : filtre à choix unique (tri)
           ------------------------------------------------- */
        else {
            // désélectionner toutes les options du groupe
            dropdown.find('.filtre-option').removeClass('selected');
            // sélectionner l’option cliquée
            $(this).addClass('selected');

            // mettre à jour le libellé avec le texte de l’option
            dropdown.find('.filtre-label').text($(this).text());

            // refermer le dropdown immédiatement
            dropdown.removeClass('is-open');
            dropdown.find('.filtre-options').slideUp(200);
        }

        // Après chaque changement, lancer le filtrage
        appliquerFiltres();
    });

    /* ==============================================================
       SECTION C – APPLICATION DES FILTRES (requête AJAX)
       ============================================================== */
    function appliquerFiltres(loadMore = false) {

        // 1️⃣  Empêcher le lancement d’une nouvelle requête tant que la précédente n’est pas terminée
        if (isLoading) {
            return;
        }

        // 2️⃣  Vérifier que l’objet filtresAjax a bien été injecté (au cas où wp_localize_script aurait échoué)
        if (typeof filtresAjax === 'undefined') {
            console.error('filtresAjax non défini !');
            return;
        }

        // 3️⃣  Si on ne charge pas « plus », on repart à la première page
        if (!loadMore) currentPage = 1;

        /* -------------------------------------------------
           Construction de l’objet envoyé à admin‑ajax.php
           ------------------------------------------------- */
        var filtresActifs = {
            action:   'filtrer_posts',          // hook PHP à appeler
            nonce:    filtresAjax.nonce,        // sécurité
            categories: [],                     // IDs des catégories sélectionnées
            formats:    [],                     // IDs des formats sélectionnés
            orderby:   'date',                  // champ de tri par défaut
            order:     'DESC',                  // sens du tri par défaut
            paged:     currentPage              // page demandée
        };

        // Récupérer les IDs des catégories cochées
        $('[data-filtre="categories"] .filtre-option.selected')
            .each(function () {
                var id = $(this).data('term-id');
                if (id) filtresActifs.categories.push(id);
            });

        // Récupérer les IDs des formats cochés
        $('[data-filtre="formats"] .filtre-option.selected')
            .each(function () {
                var id = $(this).data('term-id');
                if (id) filtresActifs.formats.push(id);
            });

        // Récupérer le critère de tri choisi (ex. date ASC/DESC)
        var tri = $('[data-filtre="tri"] .filtre-option.selected');
        if (tri.length) {
            filtresActifs.orderby = tri.data('orderby');
            filtresActifs.order   = tri.data('order');
        }

        // Vérifier que le conteneur où l’on va injecter le HTML existe
        if ($('#posts-container').length === 0) {
            console.error('#posts-container introuvable !');
            return;
        }

        /* -------------------------------------------------
           UI pendant le chargement
           ------------------------------------------------- */
        if (!loadMore) {
            // Remplacer le contenu actuel par un loader
            $('#posts-container').html('<div class="loader">Chargement</div>');
        } else {
            // Désactiver le bouton « Charger plus » et afficher un loader à l’intérieur
            $('#load-more-btn')
                .html('<span class="loader-btn">Chargement</span>')
                .prop('disabled', true);
        }

        // Indiquer qu’on est en train de charger
        isLoading = true;

        /* -------------------------------------------------
           Requête AJAX vers admin‑ajax.php
           ------------------------------------------------- */
        $.ajax({
            url:  filtresAjax.ajax_url,
            type: 'POST',
            data: filtresActifs,
            success: function (response) {

                if (response.success) {
                    // Si on charge plus (scroll/pagination) → on ajoute les nouvelles vignettes
                    if (loadMore) {
                        $('.thumbnail-container-accueil')
                            .append($(response.data.html).find('.custom-post-thumbnail'));
                    } else {
                        // Sinon on remplace tout le contenu
                        $('#posts-container').html(response.data.html);
                    }

                    // Mettre à jour le nombre total de pages (peut changer selon les filtres)
                    maxPages = response.data.max_pages;
                    updateLoadMoreButton(); // rafraîchir l’état du bouton « Charger plus »
                } else {
                    console.error('Erreur dans la réponse', response);
                    $('#posts-container')
                        .html('<p class="no-results">Erreur lors du chargement.</p>');
                }
                isLoading = false; // fin du chargement
            },
            error: function (xhr, status, err) {
                console.error('Erreur AJAX', { xhr, status, err });
                $('#posts-container')
                    .html('<p class="no-results">Erreur de connexion.</p>');
                isLoading = false;
            }
        });
    }

    /* ==============================================================
       SECTION D – BOUTON « CHARGER PLUS » (pagination infinie)
       ============================================================== */
    function updateLoadMoreButton() {
        // Si le conteneur du bouton n’existe pas encore, on le crée juste après #posts-container
        if ($('#load-more-container').length === 0) {
            $('#posts-container')
                .after('<div id="load-more-container"><button id="load-more-btn" class="load-more-button">Charger plus</button></div>');
        }

        var btn = $('#load-more-btn');

        // Désactiver le bouton lorsqu’on a atteint la dernière page
        if (currentPage >= maxPages) {
            btn.html('Charger plus')
               .prop('disabled', true)
               .css('opacity', '0.5');
        } else {
            btn.html('Charger plus')
               .prop('disabled', false)
               .css('opacity', '1');
        }

        btn.show();
        $('#load-more-container').show();
    }

    // Click sur le bouton « Charger plus » → on passe à la page suivante
    $(document).on('click', '#load-more-btn', function () {
        if (!$(this).prop('disabled')) {
            currentPage++;          // passer à la page suivante
            appliquerFiltres(true); // charger les nouvelles photos (loadMore = true)
        }
    });

    // Fermer les dropdowns si on clique n’importe où ailleurs sur la page
    $(document).on('click', function (e) {
        if (!$(e.target).closest('.filtre-dropdown').length) {
            $('.filtre-dropdown').removeClass('is-open');
            $('.filtre-options').slideUp(200);
        }
    });

    // Initialiser le bouton « Charger plus » quelques instants après le chargement de la page
    setTimeout(updateLoadMoreButton, 500);


    /* ==============================================================
       SECTION E – LIGHTBOX (affichage plein‑écran)
       ============================================================== */
    var currentIndex = 0;   // index de l’image affichée dans la lightbox
    var galleryItems = [];  // tableau contenant tous les éléments déclencheurs (.lightbox-trigger)

    // Clic sur une vignette qui possède la classe .lightbox-trigger
    $(document).on('click', '.lightbox-trigger', function (e) {
        e.preventDefault();   // empêcher le comportement par défaut du lien
        e.stopPropagation(); // empêcher la propagation du clic vers d’autres gestionnaires

        // Récupérer la collection complète des triggers (utile pour navigation)
        galleryItems = $('.lightbox-trigger').toArray();
        currentIndex = galleryItems.indexOf(this); // position de l’élément cliqué

        // Données stockées dans les attributs data‑*
        var fullImageUrl = $(this).data('full-image');
        var ref          = $(this).data('ref');
        var category     = $(this).data('category');

        // Créer le markup de la lightbox s’il n’existe pas encore
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

        // Injecter l’image et les informations dans la popup
        $('#lightbox-image').attr('src', fullImageUrl);
        $('#lightbox-ref').text('Référence: ' + ref);
        $('#lightbox-category').text(category);

        // Afficher la lightbox (overlay + popup) avec un fondu
        $('#lightbox-overlay, #lightbox-popup').fadeIn(300);
        $('body').addClass('lightbox-open'); // permet de bloquer le scroll du body, par ex.
    });

    // -------------------------------------------------
    // NAVIGATION PRÉCÉDENTE (bouton « ← »)
    // -------------------------------------------------
    $(document).on('click', '.lightbox-prev', function (e) {
        e.stopPropagation();
        // Boucler en arrière (modulo)
        currentIndex = (currentIndex - 1 + galleryItems.length) % galleryItems.length;
        var item = $(galleryItems[currentIndex]);

        $('#lightbox-image').attr('src', item.dataset.fullImage);
        $('#lightbox-ref').text('Référence: ' + item.dataset.ref);
        $('#lightbox-category').text(item.dataset.category);
    });

    // -------------------------------------------------
    // NAVIGATION SUIVANTE (bouton « → »)
    // -------------------------------------------------
    $(document).on('click', '.lightbox-next', function (e) {
        e.stopPropagation();
        currentIndex = (currentIndex + 1) % galleryItems.length;
        var item = $(galleryItems[currentIndex]);

        $('#lightbox-image').attr('src', item.dataset.fullImage);
        $('#lightbox-ref').text('Référence: ' + item.dataset.ref);
        $('#lightbox-category').text(item.dataset.category);
    });

    // -------------------------------------------------
    // FERMEUR DE LA LIGHTBOX (croix ou overlay)
    // -------------------------------------------------
    $(document).on('click', '.lightbox-close, #lightbox-overlay', function () {
        $('#lightbox-overlay, #lightbox-popup').fadeOut(300);
        $('body').removeClass('lightbox-open');
    });

    // -------------------------------------------------
    // NAVIGATION AU CLAVIER (Esc, ←, →)
    // -------------------------------------------------
    $(document).keyup(function (e) {
        if ($('#lightbox-popup').is(':visible')) {
            if (e.key === "Escape") {
                $('.lightbox-close').click();          // fermer
            } else if (e.key === "ArrowLeft") {
                $('.lightbox-prev').click();           // précédent
            } else if (e.key === "ArrowRight") {
                $('.lightbox-next').click();           // suivant
            }
        }
    });
});