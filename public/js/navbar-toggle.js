(function () {
    document.addEventListener('DOMContentLoaded', function () {
        var toggle = document.querySelector('[data-navbar-toggle]');
        if (!toggle) {
            return;
        }

        var links = document.getElementById(toggle.getAttribute('aria-controls'));
        if (!links) {
            return;
        }

        toggle.addEventListener('click', function () {
            var isOpen = links.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            toggle.classList.toggle('is-active', isOpen);
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && links.classList.contains('is-open')) {
                links.classList.remove('is-open');
                toggle.setAttribute('aria-expanded', 'false');
                toggle.classList.remove('is-active');
                toggle.focus();
            }
        });
    });
})();
