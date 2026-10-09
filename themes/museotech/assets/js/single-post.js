(() => {
    const content = document.querySelector('.single-article__content');
    const toc = document.querySelector('[data-single-toc]');

    if (!content || !toc) {
        return;
    }

    const slugify = (value) => value
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-|-$/g, '');

    const usedIds = new Set(['contenido']);
    const headings = Array.from(content.querySelectorAll('h2, h3'));
    const fallbackItem = toc.querySelector('[data-single-toc-fallback]');

    if (headings.length > 0) {
        fallbackItem?.remove();
    }

    headings.forEach((heading, index) => {
        let id = heading.id || slugify(heading.textContent) || `seccion-${index + 1}`;
        const baseId = id;
        let suffix = 2;

        while (usedIds.has(id)) {
            id = `${baseId}-${suffix}`;
            suffix += 1;
        }

        heading.id = id;
        usedIds.add(id);

        const item = document.createElement('li');
        const link = document.createElement('a');
        item.dataset.level = heading.tagName.slice(1);
        link.href = `#${id}`;
        link.textContent = heading.textContent;
        item.append(link);
        toc.append(item);
    });

    content.querySelectorAll('p').forEach((paragraph) => {
        if (!paragraph.textContent.trim() && !paragraph.querySelector('img, video, audio, iframe')) {
            paragraph.remove();
        }
    });

    const tocLinks = Array.from(toc.querySelectorAll('a'));
    const targets = tocLinks
        .map((link) => document.querySelector(link.hash))
        .filter(Boolean);

    if (!('IntersectionObserver' in window) || targets.length === 0) {
        return;
    }

    const setActiveLink = (id) => {
        tocLinks.forEach((link) => {
            const isActive = link.hash === `#${id}`;
            link.classList.toggle('is-active', isActive);
            if (isActive) {
                link.setAttribute('aria-current', 'location');
            } else {
                link.removeAttribute('aria-current');
            }
        });
    };

    const observer = new IntersectionObserver((entries) => {
        const visible = entries
            .filter((entry) => entry.isIntersecting)
            .sort((first, second) => first.boundingClientRect.top - second.boundingClientRect.top);

        if (visible[0]) {
            setActiveLink(visible[0].target.id);
        }
    }, {
        rootMargin: '-18% 0px -68% 0px',
        threshold: 0,
    });

    targets.forEach((target) => observer.observe(target));
})();
