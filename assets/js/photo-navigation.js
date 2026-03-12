/* ------------------------------------------------------------------
   photo-navigation.js — Navigation entre photos sur la page single-photo
   (consigne 16) : script extrait du template single-photo.php
   (consigne 17) : les URLs de navigation sont lues depuis
      des attributs data-url plutôt que depuis des onclick inline
   ------------------------------------------------------------------ */

jQuery(document).ready(function ($) {

    /* ----------------------------------------------------------
       NAVIGATION PRÉCÉDENT / SUIVANT
       Les boutons portent un attribut data-url avec l'URL cible.
       On lit cet attribut au clic plutôt que d'avoir l'URL en dur
       dans un onclick="window.location.href='...'" inline.
    ---------------------------------------------------------- */
    $(document).on('click', '.nav-arrow', function () {
        // Ne rien faire si le bouton est désactivé
        if ($(this).prop('disabled')) return;

        var url = $(this).data('url');
        if (url) {
            window.location.href = url;
        }
    });

    /* ----------------------------------------------------------
       PRÉVISUALISATION DE LA MINIATURE AU SURVOL
       (précédemment dans le <script> inline de single-photo.php)
    ---------------------------------------------------------- */
    var previewTimeout;

    $('.photo-nav-arrows button').hover(
        function () {
            var $thumbnail = $('.photo-nav-thumbnail');

            previewTimeout = setTimeout(function () {
                $thumbnail.addClass('show-preview');
            }, 500);
        },
        function () {
            clearTimeout(previewTimeout);
            $('.photo-nav-thumbnail').removeClass('show-preview');
        }
    );

});
