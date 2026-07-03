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
})();
