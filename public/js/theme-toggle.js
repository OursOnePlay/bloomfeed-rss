(function () {
    var STORAGE_KEY = 'bloomfeed-theme';

    function applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
    }

    document.addEventListener('DOMContentLoaded', function () {
        var toggle = document.querySelector('[data-theme-toggle]');
        if (!toggle) {
            return;
        }

        toggle.addEventListener('click', function () {
            var current = document.documentElement.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
            var next = current === 'dark' ? 'light' : 'dark';
            applyTheme(next);
            localStorage.setItem(STORAGE_KEY, next);
        });
    });
})();
