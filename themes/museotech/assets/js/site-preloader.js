(() => {
    const preloader = document.querySelector('[data-site-preloader]');
    if (!preloader) return;

    const percentage = preloader.querySelector('[data-site-preloader-percentage]');
    const assetCount = preloader.querySelector('[data-site-preloader-asset-count]');
    const startedAt = performance.now();
    const minimumVisibleTime = 180;
    const maximumLoadingTime = 7000;
    const imageGroups = new Map();
    const tasks = [];
    let loadedAssets = 0;
    let isLeaving = false;
    let maximumTimeoutId;

    const announcePreloaderHidden = () => {
        preloader.hidden = true;
        window.dispatchEvent(new CustomEvent('museotech:preloader:hidden'));
    };

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
        if (!stage.dataset.modelSrc) return false;
        const bounds = stage.getBoundingClientRect();
        return bounds.width > 0
            && bounds.height > 0
            && bounds.top < window.innerHeight
            && bounds.bottom > 0;
    });

    visibleModels.forEach((stage) => {
        tasks.push(new Promise((resolve) => {
            let modelTimeoutId;
            const observer = new MutationObserver(() => checkModelState());
            const finish = (loaded) => {
                observer.disconnect();
                window.clearTimeout(modelTimeoutId);
                resolve(loaded);
            };
            const checkModelState = () => {
                const state = stage.dataset.modelState;
                if (state === 'ready') finish(true);
                else if (state === 'error' || state === 'unsupported') finish(false);
            };

            observer.observe(stage, { attributes: true, attributeFilter: ['data-model-state'] });
            modelTimeoutId = window.setTimeout(() => finish(false), maximumLoadingTime - 500);
            checkModelState();
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
        maximumTimeoutId = window.setTimeout(resolve, maximumLoadingTime);
    });
    Promise.race([allTasks, timeout]).then(async () => {
        window.clearTimeout(maximumTimeoutId);
        const remainingVisibleTime = minimumVisibleTime - (performance.now() - startedAt);
        if (remainingVisibleTime > 0) {
            await new Promise((resolve) => window.setTimeout(resolve, remainingVisibleTime));
        }

        isLeaving = true;
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            announcePreloaderHidden();
            return;
        }

        preloader.classList.add('is-leaving');
        await new Promise((resolve) => {
            let fallback;
            const finish = () => {
                window.clearTimeout(fallback);
                preloader.removeEventListener('transitionend', onTransitionEnd);
                resolve();
            };
            const onTransitionEnd = (event) => {
                if (event.target !== preloader || event.propertyName !== 'transform') return;
                finish();
            };
            fallback = window.setTimeout(finish, 550);
            preloader.addEventListener('transitionend', onTransitionEnd);
        });
        announcePreloaderHidden();
    });
})();
