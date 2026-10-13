(() => {
    const archive = document.querySelector('.news-archive');
    const status = archive?.querySelector('.news-archive__status');

    if (!archive || !status || !window.fetch || !window.DOMParser) {
        return;
    }

    let isLoading = false;

    const revealCards = (cards) => {
        cards.forEach((card) => {
            card.classList.add('is-entering', 'is-revealed');
        });

        window.requestAnimationFrame(() => {
            window.requestAnimationFrame(() => {
                cards.forEach((card) => card.classList.remove('is-entering'));
            });
        });

        window.setTimeout(() => {
            cards.forEach((card) => card.classList.remove('is-revealed'));
        }, 650);
    };

    const updateNextControl = (link, incomingDocument) => {
        const incomingNext = incomingDocument.querySelector('.news-archive__pagination-next a');

        if (incomingNext) {
            link.href = incomingNext.href;
            link.textContent = incomingNext.textContent;
            link.removeAttribute('aria-disabled');
            return;
        }

        link.removeAttribute('href');
        link.setAttribute('aria-disabled', 'true');
        link.textContent = 'No hay más artículos';
    };

    archive.addEventListener('click', async (event) => {
        const link = event.target.closest('.news-archive__pagination a');
        const grid = archive.querySelector('.news-archive__grid');

        if (!link || !grid || isLoading || link.getAttribute('aria-disabled') === 'true') {
            return;
        }

        event.preventDefault();
        const isNext = Boolean(link.closest('.news-archive__pagination-next'));
        const originalText = link.textContent;
        isLoading = true;
        link.classList.add('is-loading');
        link.setAttribute('aria-busy', 'true');
        link.textContent = 'Cargando…';
        status.textContent = 'Cargando artículos…';

        try {
            const fragmentUrl = new URL(link.href);
            fragmentUrl.searchParams.set('museotech_archive_fragment', '1');
            const response = await fetch(fragmentUrl, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            });

            if (!response.ok || response.headers.get('X-Museotech-Archive-Fragment') !== '1') {
                throw new Error(`HTTP ${response.status}`);
            }

            const incomingDocument = new DOMParser().parseFromString(await response.text(), 'text/html');
            incomingDocument.querySelectorAll('a[href]').forEach((anchor) => {
                const href = anchor.getAttribute('href');
                if (!href.includes('museotech_archive_fragment')) return;
                const cleanUrl = new URL(href, window.location.href);
                cleanUrl.searchParams.delete('museotech_archive_fragment');
                anchor.href = cleanUrl.href;
            });
            const incomingGrid = incomingDocument.querySelector('.news-archive__grid');

            if (!incomingGrid) {
                throw new Error('No se encontró la rejilla de artículos.');
            }
            if (!grid.isConnected) return;

            const incomingCards = Array.from(incomingGrid.querySelectorAll('.news-archive-card'));

            if (isNext) {
                const existingIds = new Set(Array.from(grid.children, (card) => card.id).filter(Boolean));
                const newCards = incomingCards.filter((card) => !card.id || !existingIds.has(card.id));
                grid.append(...newCards);
                revealCards(newCards);
                updateNextControl(link, incomingDocument);
                status.textContent = `Se han cargado ${newCards.length} artículos más.`;
            } else {
                grid.replaceChildren(...incomingCards);
                revealCards(incomingCards);

                const pagination = archive.querySelector('.news-archive__pagination');
                const incomingPagination = incomingDocument.querySelector('.news-archive__pagination');
                if (pagination && incomingPagination) {
                    pagination.replaceChildren(...incomingPagination.children);
                }

                const firstArticleLink = grid.querySelector('.news-archive-card__link');
                firstArticleLink?.focus({ preventScroll: true });
                status.textContent = `Se han cargado ${incomingCards.length} artículos.`;
            }
        } catch (error) {
            link.textContent = originalText;
            if (grid.isConnected) status.textContent = 'No se han podido cargar los artículos. Inténtalo de nuevo.';
        } finally {
            isLoading = false;
            link.classList.remove('is-loading');
            link.removeAttribute('aria-busy');
        }
    });
})();
