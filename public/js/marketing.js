(function () {
    document.querySelectorAll('[data-preview]').forEach(function (button) {
        button.addEventListener('click', function () {
            var mode = button.dataset.preview;
            document.querySelectorAll('[data-preview]').forEach(function (item) {
                item.setAttribute('aria-pressed', String(item === button));
            });
            document.querySelectorAll('[data-preview-image]').forEach(function (img) {
                img.src = img.dataset.previewImage + '-' + mode + '.png';
            });
        });
    });
})();
