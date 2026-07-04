(function () {
    function csrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function post(url) {
        return fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken(),
                'Accept': 'application/json',
            },
        });
    }

    document.addEventListener('click', function (event) {
        var favoriteBtn = event.target.closest('[data-favorite-url]');
        if (favoriteBtn) {
            event.preventDefault();
            post(favoriteBtn.getAttribute('data-favorite-url')).then(function (res) {
                return res.json();
            }).then(function (data) {
                favoriteBtn.classList.toggle('is-favorite', !!data.is_favorite);
            });
            return;
        }

        var deleteForm = event.target.closest('[data-confirm]');
        if (deleteForm && !confirm(deleteForm.getAttribute('data-confirm'))) {
            event.preventDefault();
        }
    });

    // Catalogue : recherche + filtre par catégorie côté client
    document.addEventListener('DOMContentLoaded', function () {
        var grid = document.getElementById('market-grid');
        if (!grid) {
            return;
        }

        var search = document.getElementById('market-search');
        var langSelect = document.getElementById('market-lang');
        var chips = document.getElementById('market-chips');
        var emptyNotice = document.getElementById('market-empty');
        var activeCategory = '';

        function applyFilters() {
            var query = (search.value || '').toLowerCase().trim();
            var activeLang = langSelect ? langSelect.value : '';
            var visible = 0;

            grid.querySelectorAll('.market-card').forEach(function (card) {
                var matchesCategory = !activeCategory || card.getAttribute('data-category') === activeCategory;
                var matchesLang = !activeLang || card.getAttribute('data-lang') === activeLang;
                var matchesQuery = !query || card.getAttribute('data-search').indexOf(query) !== -1;
                var show = matchesCategory && matchesLang && matchesQuery;
                card.hidden = !show;
                if (show) {
                    visible++;
                }
            });

            emptyNotice.hidden = visible > 0;
        }

        search.addEventListener('input', applyFilters);

        if (langSelect) {
            langSelect.addEventListener('change', applyFilters);
        }

        chips.addEventListener('click', function (event) {
            var chip = event.target.closest('.chip');
            if (!chip) {
                return;
            }
            chips.querySelectorAll('.chip').forEach(function (c) {
                c.classList.remove('active');
            });
            chip.classList.add('active');
            activeCategory = chip.getAttribute('data-category');
            applyFilters();
        });
    });
})();
