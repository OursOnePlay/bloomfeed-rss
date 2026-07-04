(function () {
    document.addEventListener('DOMContentLoaded', function () {
        var targets = document.querySelectorAll('.reveal');
        if (!targets.length) {
            return;
        }

        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches || !('IntersectionObserver' in window)) {
            targets.forEach(function (el) { el.classList.add('is-visible'); });
            return;
        }

        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.15, rootMargin: '0px 0px -60px 0px' });

        // Stagger siblings within the same parent (e.g. the 4 feature cards, or the
        // 4 trust badges) rather than indexing across the whole page.
        var seenPerParent = new Map();
        targets.forEach(function (el) {
            var parent = el.parentElement;
            var localIndex = seenPerParent.get(parent) || 0;
            seenPerParent.set(parent, localIndex + 1);
            el.style.setProperty('--reveal-delay', ((localIndex % 4) * 90) + 'ms');
            observer.observe(el);
        });
    });
})();
