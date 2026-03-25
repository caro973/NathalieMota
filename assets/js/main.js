/* ------------------------------------------------------------------
   script.js –  Interactions générales du thème
   ------------------------------------------------------------------ */

/* --------------------------------------------------------------
   MENU BURGER MOBILE
   -------------------------------------------------------------- */


document.addEventListener('DOMContentLoaded', function () {

    /* ----------------------------------------------------------
            Récupération des éléments du menu
       ---------------------------------------------------------- */
    const menuToggle = document.getElementById('menu-toggle');   // le bouton qui ouvre/ferme le menu (souvent une icône hamburger)
    const menuListe  = document.querySelector('.menu-liste');    // la <ul> (ou <nav>) qui contient les liens du menu

    
    if (menuToggle && menuListe) {

        /* ------------------------------------------------------
           OUVERTURE / FERMETURE DU MENU
           ------------------------------------------------------ */
        menuToggle.addEventListener('click', function () {
       
            this.classList.toggle('active');
            menuListe.classList.toggle('mobile-menu-open');
            document.body.style.overflow = menuListe.classList.contains('mobile-menu-open')
                ? 'hidden'          // menu ouvert → on empêche le scroll
                : '';               // menu fermé → on laisse le scroll normal
        });

        /* ------------------------------------------------------
           FERME LE MENU QUAND ON CLIQUE SUR UN LIEN DU MENU
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

