(() => {
    const preloader = document.querySelector('[data-site-preloader]');
    if (!preloader) return;

    const percentage = preloader.querySelector('[data-site-preloader-percentage]');
    const assetCount = preloader.querySelector('[data-site-preloader-asset-count]');
    const startedAt = performance.now();
    const minimumVisibleTime = 500;
    const maximumVisibleTime = 12000;
    const imageGroups = new Map();
    const tasks = [];
    let loadedAssets = 0;
    let isLeaving = false;
    let maximumTimeoutId;

    const updateProgress = () => {
        if (isLeaving) return;
        const totalAssets = tasks.length;
        const progress = totalAssets > 0 ? Math.floor((loadedAssets / totalAssets) * 100) : 100;
        const countText = `${loadedAssets} / ${totalAssets} recursos`;

        percentage.textContent = `${progress}%`;
        assetCount.textContent = countText;
        preloader.style.setProperty('--site-preloader-progress', String(progress / 100));
        preloader.setAttribute('aria-valuenow', String(progress));
        preloader.setAttribute('aria-valuetext', `${loadedAssets} de ${totalAssets} recursos cargados`);
    };

    const waitForImage = (image) => new Promise((resolve) => {
        const complete = (loaded) => {
            image.removeEventListener('load', onLoad);
            image.removeEventListener('error', onError);
            if (!loaded || typeof image.decode !== 'function') {
                resolve(loaded && image.naturalWidth > 0);
                return;
            }
            image.decode().then(
                () => resolve(image.naturalWidth > 0),
                () => resolve(image.naturalWidth > 0),
            );
        };
        const onLoad = () => complete(true);
        const onError = () => complete(false);

        if (image.complete) {
            complete(image.naturalWidth > 0);
            return;
        }

        image.addEventListener('load', onLoad, { once: true });
        image.addEventListener('error', onError, { once: true });
        if (image.complete) complete(image.naturalWidth > 0);
    });

    Array.from(document.images).forEach((image) => {
        const source = image.currentSrc || image.src;
        if (!source) return;

        const bounds = image.getBoundingClientRect();
        const hasLayout = bounds.width > 0 && bounds.height > 0;
        const nearViewport = hasLayout && bounds.top < window.innerHeight + 120 && bounds.bottom > -120;
        if (!hasLayout || (image.loading === 'lazy' && !nearViewport)) return;

        const url = new URL(source, window.location.href).href;
        if (!imageGroups.has(url)) imageGroups.set(url, []);
        imageGroups.get(url).push(image);
    });

    imageGroups.forEach((images) => {
        tasks.push(Promise.all(images.map(waitForImage)).then((results) => results.every(Boolean)));
    });

    if (document.fonts && document.fonts.size > 0) {
        tasks.push(Promise.resolve(document.fonts.ready).then(() => true, () => false));
    }

    const visibleModels = Array.from(document.querySelectorAll('[data-model-src]')).filter((stage) => {
        const bounds = stage.getBoundingClientRect();
        return Boolean(stage.dataset.modelSrc)
            && bounds.width > 0
            && bounds.height > 0
            && bounds.top < window.innerHeight + 120
            && bounds.bottom > -120;
    });

    visibleModels.forEach((stage) => {
        tasks.push(new Promise((resolve) => {
            let timeoutId;
            const finish = (loaded) => {
                observer.disconnect();
                window.clearTimeout(timeoutId);
                resolve(loaded);
            };
            const readState = () => {
                if (stage.dataset.modelState === 'ready') finish(true);
                else if (['error', 'unsupported'].includes(stage.dataset.modelState)) finish(false);
            };
            const observer = new MutationObserver(readState);
            observer.observe(stage, { attributes: true, attributeFilter: ['data-model-state'] });
            timeoutId = window.setTimeout(() => finish(false), maximumVisibleTime - 1000);
            readState();
        }));
    });

    updateProgress();

    const allTasks = Promise.all(tasks.map((task) => task.then((loaded) => {
        if (loaded) loadedAssets += 1;
        updateProgress();
    }, () => {
        updateProgress();
    })));

    const timeout = new Promise((resolve) => {
        maximumTimeoutId = window.setTimeout(resolve, maximumVisibleTime);
    });
    Promise.race([allTasks, timeout]).then(async () => {
        window.clearTimeout(maximumTimeoutId);
        const remainingVisibleTime = minimumVisibleTime - (performance.now() - startedAt);
        if (remainingVisibleTime > 0) {
            await new Promise((resolve) => window.setTimeout(resolve, remainingVisibleTime));
        }

        isLeaving = true;
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            preloader.hidden = true;
            return;
        }

        preloader.classList.add('is-leaving');
        await new Promise((resolve) => {
            const fallback = window.setTimeout(resolve, 800);
            preloader.addEventListener('transitionend', () => {
                window.clearTimeout(fallback);
                resolve();
            }, { once: true });
        });
        preloader.hidden = true;
    });
})();
