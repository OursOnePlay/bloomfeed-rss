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

    document.addEventListener('submit', function (event) {
        var form = event.target.closest('[data-favorite-form]');
        if (!form) return;
        event.preventDefault();
        var button = form.querySelector('button');
        if (button.disabled) return;
        button.disabled = true;
        button.setAttribute('aria-busy', 'true');
        post(form.action).then(function (response) {
            if (!response.ok) throw new Error('Request failed');
            return response.json();
        }).then(function (data) {
            button.classList.toggle('is-favorite', !!data.is_favorite);
            button.setAttribute('aria-pressed', String(!!data.is_favorite));
        }).catch(function () {
            var message = document.querySelector('[data-live-message]');
            if (message) message.textContent = button.dataset.error;
        }).finally(function () {
            button.disabled = false;
            button.removeAttribute('aria-busy');
        });
    });

    document.addEventListener('click', function (event) {
        var deleteForm = event.target.closest('[data-confirm]');
        if (deleteForm && !confirm(deleteForm.getAttribute('data-confirm'))) {
            event.preventDefault();
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        var panel = document.querySelector('[data-feed-panel]');
        var media = window.matchMedia('(max-width: 720px)');
        if (panel) {
            function updatePanel() { panel.open = !media.matches; }
            updatePanel();
            media.addEventListener('change', updatePanel);
            panel.addEventListener('toggle', function () {
                if (!media.matches && !panel.open) panel.open = true;
            });
        }
        var density = document.querySelector('[data-density-toggle]');
        var list = document.querySelector('[data-reading-list]');
        if (density && list) {
            try { density.checked = localStorage.getItem('bloomfeed-density') === 'compact'; } catch (_) {}
            function applyDensity() {
                list.dataset.density = density.checked ? 'compact' : 'comfortable';
                try { localStorage.setItem('bloomfeed-density', list.dataset.density); } catch (_) {}
            }
            applyDensity();
            density.addEventListener('change', applyDensity);
        }
        document.querySelectorAll('[data-password-toggle]').forEach(function (button) {
            button.addEventListener('click', function () {
                var input = document.getElementById(button.dataset.passwordToggle);
                var show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                button.setAttribute('aria-pressed', String(show));
            });
        });
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
                c.setAttribute('aria-pressed', 'false');
            });
            chip.classList.add('active');
            chip.setAttribute('aria-pressed', 'true');
            activeCategory = chip.getAttribute('data-category');
            applyFilters();
        });
    });

    // Notifiers: show/hide the per-feed picker depending on the chosen scope
    document.addEventListener('change', function (event) {
        var select = event.target.closest('[data-scope-select]');
        if (!select) {
            return;
        }
        var picker = select.closest('form').querySelector('[data-scope-feeds]');
        if (picker) {
            picker.hidden = select.value !== 'feeds';
        }
    });
})();
