// /js/scripts.js
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('contact-modal');
    const openModalBtn = document.querySelectorAll('.open-contact-modal'); // Boutons pour ouvrir la modale
    const closeModalBtn = document.querySelector('.close-modal');
    

    // Ouvrir la modale
    openModalBtn.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            modal.style.display = 'block';
        });
    });

    // Fermer la modale
    closeModalBtn.addEventListener('click', function() {
        modal.style.display = 'none';
    });

    // Fermer la modale en cliquant en dehors
    window.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
});
