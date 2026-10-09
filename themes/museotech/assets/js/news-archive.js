(() => {
    const archive = document.querySelector('.news-archive');
    const grid = archive?.querySelector('.news-archive__grid');
    const status = archive?.querySelector('.news-archive__status');

    if (!archive || !grid || !status || !window.fetch || !window.DOMParser) {
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

        if (!link || isLoading || link.getAttribute('aria-disabled') === 'true') {
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
            const response = await fetch(link.href, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const incomingDocument = new DOMParser().parseFromString(await response.text(), 'text/html');
            const incomingGrid = incomingDocument.querySelector('.news-archive__grid');

            if (!incomingGrid) {
                throw new Error('No se encontró la rejilla de artículos.');
            }

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
            status.textContent = 'No se han podido cargar los artículos. Inténtalo de nuevo.';
        } finally {
            isLoading = false;
            link.classList.remove('is-loading');
            link.removeAttribute('aria-busy');
        }
    });
})();