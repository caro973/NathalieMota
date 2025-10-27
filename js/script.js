document.addEventListener('DOMContentLoaded', function() {
    const openModalBtn = document.querySelectorAll('.open-contact-modal');
    const modal = document.getElementById('contact-modal');
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

    // Fermer la modale en cliquant à l'extérieur
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
});

