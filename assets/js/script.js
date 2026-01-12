 // Menu burger mobile
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menu-toggle');
    const menuListe = document.querySelector('.menu-liste');

    if (menuToggle && menuListe) {
        menuToggle.addEventListener('click', function() {
            this.classList.toggle('active');
            menuListe.classList.toggle('mobile-menu-open');
            document.body.style.overflow = menuListe.classList.contains('mobile-menu-open') ? 'hidden' : '';
        });

        // Fermer le menu quand on clique sur un lien
        const menuLinks = menuListe.querySelectorAll('a');
        menuLinks.forEach(link => {
            link.addEventListener('click', function() {
                menuToggle.classList.remove('active');
                menuListe.classList.remove('mobile-menu-open');
                document.body.style.overflow = '';
            });
        });
    }
}); 

////////////////////////FONCTION MODALE CONTACT/////////////////////////

// Fonctions pour la modale de contact rend la modale visible
function openContactModal(reference) {
    document.getElementById('contact-modal').style.display = 'block';
    
// Pré-remplir le champ référence si disponible
    if (reference) {
        setTimeout(function() {
            var refField = document.querySelector('input[name="ref-photo"]');
            if (refField) {
                refField.value = reference;
            }
        }, 100);
    }
}
function closeContactModal() {
    document.getElementById('contact-modal').style.display = 'none';
}

// Fermer la modale en cliquant en dehors
window.addEventListener('click', function(event) {
    var modal = document.getElementById('contact-modal');
    if (event.target === modal) {
        closeContactModal();
    }

// Ouvrir la modale depuis le menu
    var contactLinks = document.querySelectorAll('.contact-link');
    contactLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            var reference = link.getAttribute('data-reference');
            openContactModal(reference);
        });
    });

});