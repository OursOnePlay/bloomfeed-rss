(function () {
    var AUTOPLAY_DELAY = 4500;

    document.addEventListener('DOMContentLoaded', function () {
        var thumbs = document.querySelectorAll('.screenshot-thumb');
        var mainImg = document.getElementById('screenshotMainImg');
        var caption = document.getElementById('screenshotCaption');

        if (!thumbs.length || !mainImg) {
            return;
        }

        var index = 0;
        var autoplay = null;

        function activate(i) {
            thumbs.forEach(function (thumb) { thumb.classList.remove('active'); });
            thumbs[i].classList.add('active');
            mainImg.src = thumbs[i].getAttribute('data-img');
            if (caption) {
                caption.textContent = thumbs[i].getAttribute('data-caption');
            }
            index = i;
        }

        function startAutoplay() {
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                return;
            }
            autoplay = setInterval(function () {
                activate((index + 1) % thumbs.length);
            }, AUTOPLAY_DELAY);
        }

        function resetAutoplay() {
            if (autoplay) {
                clearInterval(autoplay);
            }
            startAutoplay();
        }

        thumbs.forEach(function (thumb, i) {
            thumb.addEventListener('click', function () {
                activate(i);
                resetAutoplay();
            });
        });

        startAutoplay();
    });
})();
