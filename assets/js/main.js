/* ------------------------------------------------------------------
   script.js –  Interactions générales du thème
   ------------------------------------------------------------------ */

/* --------------------------------------------------------------
   MENU BURGER MOBILE
   -------------------------------------------------------------- */

/* 
   `DOMContentLoaded` se déclenche quand le DOM (la structure HTML) 
   est complètement chargé, mais avant que les images, CSS, etc. 
   soient forcément terminés. C’est le bon moment pour attacher 
   nos écouteurs d’événements. 
*/
document.addEventListener('DOMContentLoaded', function () {

    /* ----------------------------------------------------------
            Récupération des éléments du menu
       ---------------------------------------------------------- */
    const menuToggle = document.getElementById('menu-toggle');   // le bouton qui ouvre/ferme le menu (souvent une icône hamburger)
    const menuListe  = document.querySelector('.menu-liste');    // la <ul> (ou <nav>) qui contient les liens du menu

    /* ----------------------------------------------------------
            Vérifier que les deux éléments existent réellement
            (au cas où le thème serait utilisé sur une page qui n’a
            pas de menu burger). 
       ---------------------------------------------------------- */
    if (menuToggle && menuListe) {

        /* ------------------------------------------------------
           OUVERTURE / FERMETURE DU MENU
           ------------------------------------------------------ */
        menuToggle.addEventListener('click', function () {
            /* 
               - `this` = le bouton qui vient d’être cliqué.
               - `classList.toggle('active')` ajoute la classe *active* si elle
                 n’est pas présente, ou la retire si elle l’est déjà.
               - On applique la même logique à la liste du menu : 
                 la classe *mobile-menu-open* indique qu’on veut la voir.
               - Enfin, on bloque le scrolling du corps de la page quand le
                 menu est ouvert (`overflow: hidden`). Quand il est fermé,
                 on remet la valeur par défaut (`''`).
            */
            this.classList.toggle('active');
            menuListe.classList.toggle('mobile-menu-open');
            document.body.style.overflow = menuListe.classList.contains('mobile-menu-open')
                ? 'hidden'          // menu ouvert → on empêche le scroll
                : '';               // menu fermé → on laisse le scroll normal
        });

        /* ------------------------------------------------------
           FERMEZ LE MENU QUAND ON CLIQUE SUR UN LIEN DU MENU
           ------------------------------------------------------ */
        const menuLinks = menuListe.querySelectorAll('a'); // tous les <a> du menu
        menuLinks.forEach(link => {
            link.addEventListener('click', function () {
                // On retire les classes qui indiquaient que le menu était ouvert
                menuToggle.classList.remove('active');
                menuListe.classList.remove('mobile-menu-open');
                // Et on réautorise le scroll de la page
                document.body.style.overflow = '';
            });
        });
    }
});   // ← fin du DOMContentLoaded



/* --------------------------------------------------------------
   MODALE DE CONTACT
   -------------------------------------------------------------- */

/**
 * Ouvre la modale de contact.
 *
 * @param {string|null} reference – Si on fournit une référence (par ex.
 *                                 le numéro d’une photo), on la
 *                                 pré‑remplit dans le champ du formulaire.
 */
function openContactModal(reference) {
    //    Récupérer le conteneur de la modale
    const modal = document.getElementById('contact-modal');
    if (!modal) return;               // si la modale n’existe pas → on sort

    //    Rendre la modale visible (elle est cachée en CSS avec display:none)
    modal.style.display = 'block';

    //    Si on a reçu une référence, on la place dans le champ du formulaire
    if (reference) {
        // on attend 100 ms pour être sûr que le champ est bien présent
        setTimeout(() => {
            const refField = document.querySelector('input[name="ref-photo"]');
            if (refField) {
                refField.value = reference;   // remplissage du champ
            }
        }, 100);
    }
}

/**
 * Ferme la modale de contact.
 */
function closeContactModal() {
    const modal = document.getElementById('contact-modal');
    if (modal) {
        modal.style.display = 'none';   // masquer la modale
    }
}



/* --------------------------------------------------------------
   GESTION DES ÉVÉNEMENTS GLOBAUX
   -------------------------------------------------------------- */

/**
 * Ici on écoute **tous** les clics sur la fenêtre (`window`).
 * Cela nous permet de gérer deux choses :
 *   1. Fermer la modale lorsqu’on clique en dehors d’elle.
 *   2. Attacher les comportements d’ouverture de la modale aux liens
 *      qui portent la classe `.contact-link`.
 */
window.addEventListener('click', function (event) {

    /* ------------------------------------------------------
            FERME LA MODALE SI ON CLIQUE EN DEHORS
       ------------------------------------------------------ */
    const modal = document.getElementById('contact-modal');
    // `event.target` est l’élément qui a reçu le clic.
    // Si cet élément **est exactement** la modale (c’est‑à‑dire le fond sombre),
    // on la ferme.
    if (modal && event.target === modal) {
        closeContactModal();
    }

    /* ------------------------------------------------------
           OUVRE LA MODALE DEPUIS LES LIENS « CONTACT »
       ------------------------------------------------------ */
    const contactLinks = document.querySelectorAll('.contact-link');
    contactLinks.forEach(link => {
        // Chaque lien reçoit son propre écouteur de clic
        link.addEventListener('click', function (e) {
            e.preventDefault();   // empêcher le comportement par défaut du lien (navigation)

            // On récupère la référence stockée dans l’attribut data‑reference du lien
            const reference = link.getAttribute('data-reference');

            // On ouvre la modale en lui passant la référence (ou null si aucune)
            openContactModal(reference);
        });
    });
});