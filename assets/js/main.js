/* ------------------------------------------------------------------
   script.js –  Interactions générales du thème
   ------------------------------------------------------------------ */

/* --------------------------------------------------------------
   MENU BURGER MOBILE
   -------------------------------------------------------------- */

/* 
   `DOMContentLoaded` se déclenche quand le DOM (la structure HTML) 
   est complètement chargé, mais avant que les images, CSS, etc. 
   soient forcément terminés. C'est le bon moment pour attacher 
   nos écouteurs d'événements. 
*/
document.addEventListener('DOMContentLoaded', function () {

    /* ----------------------------------------------------------
            Récupération des éléments du menu
       ---------------------------------------------------------- */
    const menuToggle = document.getElementById('menu-toggle');   // le bouton qui ouvre/ferme le menu (souvent une icône hamburger)
    const menuListe  = document.querySelector('.menu-liste');    // la <ul> (ou <nav>) qui contient les liens du menu

    /* ----------------------------------------------------------
            Vérifier que les deux éléments existent réellement
            (au cas où le thème serait utilisé sur une page qui n'a
            pas de menu burger). 
       ---------------------------------------------------------- */
    if (menuToggle && menuListe) {

        /* ------------------------------------------------------
           OUVERTURE / FERMETURE DU MENU
           ------------------------------------------------------ */
        menuToggle.addEventListener('click', function () {
            /* 
               - `this` = le bouton qui vient d'être cliqué.
               - `classList.toggle('active')` ajoute la classe *active* si elle
                 n'est pas présente, ou la retire si elle l'est déjà.
               - On applique la même logique à la liste du menu : 
                 la classe *mobile-menu-open* indique qu'on veut la voir.
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


// CORRIGÉ (consigne 9) : openContactModal(), closeContactModal() et leur
// gestionnaire d'événements window 'click' ont été supprimés d'ici.
// Ces fonctions sont désormais définies uniquement dans modal-contact.js
// afin d'éviter les conflits liés à la double déclaration.