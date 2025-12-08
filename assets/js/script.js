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

// Fonctions pour la modale de contact
function openContactModal(reference) {
    document.getElementById('contact-modal').style.display = 'block';
    
    // Pré-remplir le champ référence si disponible
    if (reference) {
        setTimeout(function() {
            var refField = document.querySelector('input[name="your-reference"]');
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
window.onclick = function(event) {
    var modal = document.getElementById('contact-modal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}

    // Ouvrir la modale depuis le menu
jQuery(document).ready(function($) {
    $(document).on('click', '.contact-link', function(e) {
        e.preventDefault();
        openContactModal();
    });
});