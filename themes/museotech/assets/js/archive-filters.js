(() => {
    const archive = document.querySelector('.news-archive, .repository-archive, .podcast-archive');
    const filters = archive?.querySelector('.news-archive__filters, .repository-archive__filters, .podcast-archive__filters');
    const results = archive?.querySelector('[data-archive-results]');
    const status = archive?.querySelector('.news-archive__status, .repository-archive__status, .podcast-archive__status');
    if (!archive || !results || !status || !window.fetch) return;

    const archivePath = new URL(archive.dataset.archiveBase).pathname;
    const itemSelector = archive.matches('.news-archive')
        ? '.news-archive-card'
        : archive.matches('.repository-archive') ? '.repository-record' : '.podcast-archive-card';
    let currentRequest = null;

    const updateActiveFilter = (url) => {
        if (!filters) return;
        const selected = new URL(url, window.location.href);
        filters.querySelectorAll('a[href]').forEach((link) => {
            const candidate = new URL(link.href);
            const active = candidate.search === selected.search;
            link.classList.toggle('is-active', active);
            if (active) link.setAttribute('aria-current', 'page');
            else link.removeAttribute('aria-current');
        });
    };

    const loadResults = async (url, addHistory, scrollToResults = false) => {
        currentRequest?.abort();
        const request = new AbortController();
        currentRequest = request;
        results.setAttribute('aria-busy', 'true');
        status.textContent = 'Actualizando resultados…';
        archive.dispatchEvent(new CustomEvent('museotech:archive-filter-start'));

        try {
            const fragmentUrl = new URL(url, window.location.href);
            fragmentUrl.searchParams.set('museotech_archive_fragment', '1');
            const response = await fetch(fragmentUrl, {
                credentials: 'same-origin',
                signal: request.signal,
                headers: { Accept: 'text/html' },
            });
            if (!response.ok || response.headers.get('X-Museotech-Archive-Fragment') !== '1') {
                throw new Error(`Respuesta de archivo no válida: ${response.status}`);
            }

            const html = await response.text();
            if (request !== currentRequest) return;
            const template = document.createElement('template');
            template.innerHTML = html;
            if (!template.content.querySelector(`${itemSelector}, .news-archive__empty, .repository-archive__empty, .podcast-archive__empty`)) {
                throw new Error('La respuesta no contiene resultados de archivo.');
            }
            template.content.querySelectorAll('a[href]').forEach((link) => {
                const href = link.getAttribute('href');
                if (!href.includes('museotech_archive_fragment')) return;
                const cleanUrl = new URL(href, window.location.href);
                cleanUrl.searchParams.delete('museotech_archive_fragment');
                link.href = cleanUrl.href;
            });

            results.replaceChildren(...template.content.childNodes);
            if (addHistory) window.history.pushState({ museotechArchiveFilter: true }, '', url);
            updateActiveFilter(url);
            const count = results.querySelectorAll(itemSelector).length;
            status.textContent = count === 0
                ? 'No hay resultados para este filtro.'
                : count === 1 ? 'Se muestra 1 resultado.' : `Se muestran ${count} resultados en esta página.`;
            archive.dispatchEvent(new CustomEvent('museotech:archive-results-updated'));
            window.ScrollTrigger?.refresh();
            if (scrollToResults) {
                const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                results.scrollIntoView({ behavior: reducedMotion ? 'auto' : 'smooth', block: 'start' });
            }
        } catch (error) {
            if (error.name !== 'AbortError') {
                status.textContent = 'No se han podido actualizar los resultados. Inténtalo de nuevo.';
            }
        } finally {
            if (request === currentRequest) {
                currentRequest = null;
                results.removeAttribute('aria-busy');
            }
        }
    };

    archive.addEventListener('click', (event) => {
        if (!(event.target instanceof Element) || event.defaultPrevented || event.button !== 0
            || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;

        const link = event.target.closest('a[href]');
        const isFilter = filters?.contains(link) || link?.matches('.repository-record__badge--format');
        const isPagination = Boolean(link?.closest('.repository-archive__records .navigation.pagination, .podcast-archive__episodes .navigation.pagination'));
        if (!link || (!isFilter && !isPagination)
            || link.hasAttribute('download') || (link.target && link.target !== '_self')) return;

        const destination = new URL(link.href);
        destination.searchParams.delete('museotech_archive_fragment');
        if (destination.origin !== window.location.origin || !destination.pathname.startsWith(archivePath)) return;

        event.preventDefault();
        if (destination.href !== window.location.href) loadResults(destination.href, true, isPagination);
    });

    window.addEventListener('popstate', () => {
        if (window.location.pathname.startsWith(archivePath)) loadResults(window.location.href, false);
    });
})();
