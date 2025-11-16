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

document.addEventListener('DOMContentLoaded', function() {
    const categorieFilter = document.getElementById('categorie-filter');
    const formatFilter = document.getElementById('format-filter');
    const sortFilter = document.getElementById('sort-filter');
    const loadMoreBtn = document.getElementById('load-more-posts');
    const container = document.querySelector('.thumbnail-container-accueil');
    const currentPageInput = document.getElementById('current-page');
    const currentCategorieInput = document.getElementById('current-categorie');
    const currentFormatInput = document.getElementById('current-format');
    const currentSortInput = document.getElementById('current-sort');

    // Fonction pour filtrer les photos
    function filterPhotos() {
        const categorie = categorieFilter.value;
        const format = formatFilter.value;
        const sort = sortFilter.value;

        // Sauvegarder les filtres actuels
        currentCategorieInput.value = categorie;
        currentFormatInput.value = format;
        currentSortInput.value = sort;
        currentPageInput.value = '1';

        fetch(ajaxurl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'filter_photos',
                categorie: categorie,
                format: format,
                sort: sort,
                page: 1
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                container.innerHTML = data.data.html;
                
                // Afficher/masquer le bouton "Charger plus"
                if (data.data.max_pages > 1) {
                    loadMoreBtn.style.display = 'block';
                    loadMoreBtn.setAttribute('data-max-pages', data.data.max_pages);
                } else {
                    loadMoreBtn.style.display = 'none';
                }
            }
        })
        .catch(error => console.error('Erreur:', error));
    }

    // Fonction pour charger plus de photos
    function loadMorePhotos() {
        const currentPage = parseInt(currentPageInput.value);
        const nextPage = currentPage + 1;
        const maxPages = parseInt(loadMoreBtn.getAttribute('data-max-pages'));
        const categorie = currentCategorieInput.value;
        const format = currentFormatInput.value;
        const sort = currentSortInput.value;

        // Désactiver le bouton pendant le chargement
        loadMoreBtn.disabled = true;
        loadMoreBtn.textContent = 'Chargement...';

        fetch(ajaxurl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'load_more_photos',
                page: nextPage,
                categorie: categorie,
                format: format,
                sort: sort
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data.html) {
                // Ajouter les nouvelles photos à la suite
                container.insertAdjacentHTML('beforeend', data.data.html);
                currentPageInput.value = nextPage;

                // Masquer le bouton si on a atteint la dernière page
                if (nextPage >= maxPages) {
                    loadMoreBtn.style.display = 'none';
                } else {
                    loadMoreBtn.disabled = false;
                    loadMoreBtn.textContent = 'Charger plus';
                }
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            loadMoreBtn.disabled = false;
            loadMoreBtn.textContent = 'Charger plus';
        });
    }

    // Event listeners
    if (categorieFilter) categorieFilter.addEventListener('change', filterPhotos);
    if (formatFilter) formatFilter.addEventListener('change', filterPhotos);
    if (sortFilter) sortFilter.addEventListener('change', filterPhotos);
    if (loadMoreBtn) loadMoreBtn.addEventListener('click', loadMorePhotos);
});

// Initialisation de Select2 pour chaque menu déroulant
document.addEventListener('DOMContentLoaded', function() {
    // Sélectionner tous les éléments avec la classe 'choices-custom'
    const choicesElements = document.querySelectorAll('.choices-custom');

    // Initialiser Choices.js sur chaque élément
    choicesElements.forEach(function(choicesElement) {
        new Choices(choicesElement, {
            removeItemButton: false,
            searchEnabled: false,
            shouldSort: false,
            placeholder: true,
             placeholderValue: choicesElement.dataset.placeholder || '',
            itemSelectText: ''
        });
    });
});



