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
    // Requests are filtered on the server; only the requested page is rendered.
    document.addEventListener('DOMContentLoaded', function () {
        var form = document.getElementById('catalog-filters');
        if (!form) return;
        var grid = document.getElementById('market-grid');
        var more = document.getElementById('catalog-more');
        var error = document.getElementById('catalog-error');
        var activeRequest;
        var timer;
        document.getElementById('catalog-fallback').hidden = true;
        function load(url, append) {
            if (activeRequest) activeRequest.abort();
            var controller = new AbortController();
            activeRequest = controller;
            grid.setAttribute('aria-busy', 'true');
            more.setAttribute('aria-disabled', 'true');
            error.hidden = true;
            fetch(url, { headers: { Accept: 'application/json' }, signal: controller.signal })
                .then(function (response) { if (!response.ok) throw new Error('Request failed'); return response.json(); })
                .then(function (data) {
                    if (controller !== activeRequest) return;
                    if (append) grid.insertAdjacentHTML('beforeend', data.html);
                    else grid.innerHTML = data.html;
                    more.hidden = !data.next;
                    more.href = data.next || '';
                    document.getElementById('market-empty').hidden = data.total > 0;
                    if (!append) history.replaceState(null, '', url);
                }).catch(function (reason) {
                    if (reason.name !== 'AbortError' && controller === activeRequest) error.hidden = false;
                }).finally(function () {
                    if (controller === activeRequest) {
                        activeRequest = null;
                        grid.removeAttribute('aria-busy');
                        more.removeAttribute('aria-disabled');
                    }
                });
        }
        function filter() {
            clearTimeout(timer);
            var params = new URLSearchParams(new FormData(form));
            load(form.action + '?' + params.toString(), false);
        }
        form.addEventListener('submit', function (event) {
            event.preventDefault();
            if (event.submitter && event.submitter.name === 'category') form.querySelector('input[name=category]').value = event.submitter.value;
            document.querySelectorAll('[data-category]').forEach(function (chip) {
                if (!chip.matches('button')) return;
                var selected = chip.dataset.category === form.querySelector('input[name=category]').value;
                chip.classList.toggle('active', selected);
                chip.setAttribute('aria-pressed', String(selected));
            });
            filter();
        });
        document.getElementById('market-search').addEventListener('input', function () { clearTimeout(timer); timer = setTimeout(filter, 250); });
        document.getElementById('market-lang').addEventListener('change', filter);
        more.addEventListener('click', function (event) { event.preventDefault(); if (!activeRequest) load(more.href, true); });
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
