/* ------------------------------------------------------------------
   modal-contact.js –  Gestion de la modale de contact
   ------------------------------------------------------------------ */

function openContactModal(reference) {
    const modal = document.getElementById('contact-modal');
    if (!modal) return;
    modal.style.display = 'block';

    if (reference) {
        setTimeout(() => {
            const refField = document.querySelector('input[name="ref-photo"]');
            if (refField) refField.value = reference;
        }, 100);
    }
}

function closeContactModal() {
    const modal = document.getElementById('contact-modal');
    if (modal) modal.style.display = 'none';
}

/* Initialisation des événements (click sur les liens .contact‑link) */
window.addEventListener('click', function (event) {
    const modal = document.getElementById('contact-modal');

    // Fermer la modale si on clique en dehors
    if (modal && event.target === modal) {
        closeContactModal();
    }

    // Ouvrir la modale depuis les liens .contact-link
    document.querySelectorAll('.contact-link').forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const reference = link.getAttribute('data-reference');
            openContactModal(reference);
        });
    });
});