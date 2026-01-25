/* ------------------------------------------------------------------
   menu.js –  Gestion du menu burger (ouverture / fermeture) sur mobile
   ------------------------------------------------------------------ */

/**
 * Initialise le menu burger.
 * Cette fonction est appelée depuis `main.js` lorsque le DOM est prêt.
 */
function initBurgerMenu() {
    // Le DOM doit être chargé avant de chercher les éléments.
    const menuToggle = document.getElementById('menu-toggle'); // bouton hamburger
    const menuListe  = document.querySelector('.menu-liste'); // <ul> ou <nav> contenant les liens

    // Si l’un des deux éléments n’existe pas (ex. page sans menu), on ne fait rien.
    if (!menuToggle || !menuListe) {
        return;
    }

    /* --------------------------------------------------------------
       OUVERTURE / FERMETURE DU MENU
       -------------------------------------------------------------- */
    menuToggle.addEventListener('click', function () {
        // `this` = le bouton qui vient d’être cliqué.
        this.classList.toggle('active');                     // ajoute / retire la classe active
        menuListe.classList.toggle('mobile-menu-open');      // montre / cache la liste

        // Bloquer le scroll du corps quand le menu est ouvert.
        document.body.style.overflow = menuListe.classList.contains('mobile-menu-open')
            ? 'hidden'   // menu ouvert → on empêche le scroll
            : '';        // menu fermé → on remet le comportement par défaut
    });

    /* --------------------------------------------------------------
       FERME LE MENU QUAND UN LIEN EST CLICKÉ
       -------------------------------------------------------------- */
    const menuLinks = menuListe.querySelectorAll('a'); // tous les <a> du menu
    menuLinks.forEach(link => {
        link.addEventListener('click', function () {
            // Retirer les classes qui indiquaient que le menu était ouvert.
            menuToggle.classList.remove('active');
            menuListe.classList.remove('mobile-menu-open');
            // Autoriser à nouveau le scroll de la page.
            document.body.style.overflow = '';
        });
    });
}

/* Exporter la fonction pour qu’elle puisse être appelée depuis main.js */
export { initBurgerMenu };