(() => {
    const gsap = window.gsap;
    const scrollTrigger = window.ScrollTrigger;
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (!gsap || prefersReducedMotion) return;
    if (scrollTrigger) gsap.registerPlugin(scrollTrigger);

    const splitIntoLines = (heading, originalText) => {
        const measuredWords = originalText.split(' ').map((word) => {
            const span = document.createElement('span');
            span.className = 'home-text-animation__measure-word';
            span.textContent = word;
            return span;
        });

        heading.replaceChildren();
        measuredWords.forEach((word, index) => {
            heading.append(word);
            if (index < measuredWords.length - 1) heading.append(document.createTextNode(' '));
        });

        const lineGroups = [];
        measuredWords.forEach((word) => {
            const top = word.getBoundingClientRect().top;
            let line = lineGroups[lineGroups.length - 1];
            if (!line || Math.abs(line.top - top) > 1) {
                line = { top, words: [] };
                lineGroups.push(line);
            }
            line.words.push(word.textContent);
        });

        if (!heading.hasAttribute('aria-label')) heading.setAttribute('aria-label', originalText);
        heading.replaceChildren();

        return lineGroups.map((line, index) => {
            const mask = document.createElement('span');
            mask.className = 'home-text-animation__mask';
            mask.setAttribute('aria-hidden', 'true');

            const inner = document.createElement('span');
            inner.className = 'home-text-animation__line';
            inner.textContent = line.words.join(' ');
            mask.append(inner);
            heading.append(mask);
            if (index < lineGroups.length - 1) heading.append(document.createTextNode(' '));
            return inner;
        });
    };

    const getScrollStart = (element) => element.closest('[data-scroll-trigger-start]')?.dataset.scrollTriggerStart || 'top 92%';

    const animateGroup = (group, titleSelector, fadeSelector, useScrollTrigger, onComplete) => {
        if (useScrollTrigger && !scrollTrigger) return;

        const heading = group.querySelector(titleSelector);
        const originalText = heading ? heading.textContent.trim().replace(/\s+/g, ' ') : '';
        const fades = Array.from(group.querySelectorAll(fadeSelector));
        if (!heading && !fades.length) return;
        let completionNotified = false;
        const notifyComplete = () => {
            if (completionNotified) return;
            completionNotified = true;
            onComplete?.();
        };

        const record = {
            complete: false,
            fades,
            heading,
            lines: [],
            originalText,
            played: false,
            timeline: null,
            width: heading ? heading.getBoundingClientRect().width : 0,
        };

        const buildTimeline = () => {
            if (heading) {
                heading.replaceChildren(document.createTextNode(originalText));
                record.lines = splitIntoLines(heading, originalText);
            }

            if (record.complete) {
                gsap.set([...record.lines, ...fades], { clearProps: 'transform,opacity,visibility' });
                notifyComplete();
                return;
            }

            if (record.lines.length) gsap.set(record.lines, { yPercent: 110 });
            if (fades.length) gsap.set(fades, { autoAlpha: 0, y: 16 });

            const timeline = gsap.timeline(useScrollTrigger ? {
                scrollTrigger: {
                    trigger: group,
                    start: getScrollStart(group),
                    once: true,
                    onEnter: () => {
                        record.played = true;
                    },
                },
            } : {});
            record.timeline = timeline;

            if (record.lines.length) {
                timeline.to(record.lines, {
                    yPercent: 0,
                    duration: 0.88,
                    stagger: 0.1,
                    ease: 'power3.out',
                });
            }

            if (fades.length) {
                timeline.to(fades, {
                    autoAlpha: 1,
                    y: 0,
                    duration: 0.68,
                    stagger: 0.1,
                    ease: 'power2.out',
                });
            }

            timeline.eventCallback('onStart', () => {
                record.played = true;
            });
            timeline.eventCallback('onComplete', () => {
                record.complete = true;
                gsap.set([...record.lines, ...fades], { clearProps: 'transform,opacity,visibility' });
                notifyComplete();
            });
        };

        buildTimeline();

        if (heading && 'ResizeObserver' in window) {
            const resizeObserver = new ResizeObserver((entries) => {
                const nextWidth = entries[0].contentRect.width;
                if (Math.abs(nextWidth - record.width) < 2) return;
                record.width = nextWidth;

                if (record.timeline) {
                    record.timeline.scrollTrigger?.kill();
                    record.timeline.kill();
                }
                if (record.played) record.complete = true;
                buildTimeline();
                if (scrollTrigger) scrollTrigger.refresh();
            });
            resizeObserver.observe(heading);
        }

        return record;
    };

    const isListingPage = Boolean(document.querySelector('.news-archive, .podcast-archive, .repository-archive, .collaborators-page'));
    const listingItems = isListingPage ? Array.from(document.querySelectorAll([
            '.news-archive__grid > *',
            '.news-archive__pagination',
            '.news-archive__empty',
            '.podcast-archive__episodes-header',
            '.podcast-archive__grid > *',
            '.podcast-archive__episodes > .navigation',
            '.podcast-archive__empty',
            '.repository-archive__filters',
            '.repository-archive__sidebar',
            '.repository-archive__records > *',
            '.collaborators-page__grid',
            '.collaborators-page__list > .navigation',
            '.collaborators-page__empty',
        ].join(', '))) : [];

    if (scrollTrigger && listingItems.length) gsap.set(listingItems, { autoAlpha: 0, y: 24 });

    const initListingReveals = () => {
        if (!scrollTrigger || !listingItems.length) return;

        scrollTrigger.batch(listingItems, {
            start: 'top 92%',
            once: true,
            onEnter: (batch) => gsap.to(batch, {
                autoAlpha: 1,
                y: 0,
                duration: 0.72,
                stagger: 0.12,
                ease: 'power2.out',
                overwrite: true,
            }),
        });
        scrollTrigger.refresh();
    };

    const initTextReveals = () => {
        const hero = document.querySelector('[data-hero]');
        if (hero) animateGroup(hero, '[data-hero-title]', '[data-hero-fade]', false);

        const banners = Array.from(document.querySelectorAll('.section-banner'));
        if (banners.length) {
            let unfinishedBanners = banners.length;
            const onBannerComplete = () => {
                unfinishedBanners -= 1;
                if (unfinishedBanners === 0) initListingReveals();
            };

            banners.forEach((banner) => {
                const record = animateGroup(
                    banner,
                    '.section-banner__title',
                    '.section-banner__eyebrow, .section-banner__description, nav',
                    false,
                    onBannerComplete,
                );
                if (!record) onBannerComplete();
            });
        } else {
            initListingReveals();
        }

        document.querySelectorAll('[data-reveal-group]').forEach((group) => {
            animateGroup(group, '[data-scroll-title]', '[data-scroll-fade]', true);
        });

        if (scrollTrigger) scrollTrigger.refresh();
    };

    const initCounters = () => {
        if (!scrollTrigger) return;

        const buildOdometer = (element, digits) => {
            element.classList.add('home-counter');
            element.replaceChildren();

            const digitWindows = Array.from(digits, (digit) => {
                const windowElement = document.createElement('span');
                windowElement.className = 'home-counter__digit';
                windowElement.setAttribute('aria-hidden', 'true');

                const track = document.createElement('span');
                track.className = 'home-counter__track';
                for (let value = 0; value <= 9; value += 1) {
                    const cell = document.createElement('span');
                    cell.className = 'home-counter__value';
                    cell.textContent = String(value);
                    track.append(cell);
                }

                windowElement.append(track);
                element.append(windowElement);
                return { digit: Number(digit), windowElement, track };
            });

            gsap.set(digitWindows.map(({ windowElement }) => windowElement), {
                autoAlpha: 0,
                yPercent: 110,
            });

            const timeline = gsap.timeline({ paused: true });
            digitWindows.forEach(({ digit, windowElement, track }) => {
                timeline.to(windowElement, {
                    autoAlpha: 1,
                    yPercent: 0,
                    duration: 0.34,
                    ease: 'power3.out',
                });

                if (digit > 0) {
                    timeline.to(track, {
                        yPercent: digit * -10,
                        duration: 0.54 + digit * 0.035,
                        ease: 'power2.inOut',
                    }, '<0.04');
                }
            });

            scrollTrigger.create({
                trigger: element,
                start: getScrollStart(element),
                once: true,
                onEnter: () => timeline.play(0),
            });
        };

        document.querySelectorAll('[data-count][data-count-to]').forEach((element) => {
            const rawTarget = element.getAttribute('data-count-to');
            const target = Number(rawTarget);
            if (rawTarget === null || rawTarget.trim() === '' || !Number.isFinite(target)) return;

            const markupValue = element.textContent.trim();
            const zeroPadding = /^0\d+$/.test(markupValue) ? markupValue.length : 0;
            if (zeroPadding) {
                buildOdometer(element, String(Math.round(target)).padStart(zeroPadding, '0'));
                return;
            }

            const formatValue = (value) => String(Math.round(value)).padStart(zeroPadding, '0');
            const counter = { value: 0 };
            element.textContent = formatValue(0);

            scrollTrigger.create({
                trigger: element,
                start: getScrollStart(element),
                once: true,
                onEnter: () => gsap.to(counter, {
                    value: target,
                    duration: 1.2,
                    ease: 'power1.out',
                    onUpdate: () => {
                        element.textContent = formatValue(counter.value);
                    },
                    onComplete: () => {
                        element.textContent = formatValue(target);
                    },
                }),
            });
        });
    };

    const initMagneticElements = () => {
        if (!window.matchMedia('(pointer: fine)').matches || typeof gsap.quickTo !== 'function') return;

        document.querySelectorAll('[data-magnetic]').forEach((element) => {
            const moveX = gsap.quickTo(element, 'x', { duration: 0.38, ease: 'power3.out' });
            const moveY = gsap.quickTo(element, 'y', { duration: 0.38, ease: 'power3.out' });

            element.addEventListener('pointermove', (event) => {
                const bounds = element.getBoundingClientRect();
                const x = ((event.clientX - bounds.left - bounds.width / 2) / bounds.width) * 36;
                const y = ((event.clientY - bounds.top - bounds.height / 2) / bounds.height) * 22;
                moveX(gsap.utils.clamp(-18, 18, x));
                moveY(gsap.utils.clamp(-11, 11, y));
            });

            const reset = () => {
                moveX(0);
                moveY(0);
            };
            element.addEventListener('pointerleave', reset);
            element.addEventListener('blur', reset);
        });
    };

    const startEffects = () => {
        initMagneticElements();
        const fontsReady = document.fonts ? document.fonts.ready : Promise.resolve();
        Promise.resolve(fontsReady).then(() => {
            initTextReveals();
            initCounters();
        });
    };

    const preloader = document.querySelector('[data-site-preloader]');
    if (!preloader || preloader.hidden) {
        startEffects();
    } else {
        window.addEventListener('museotech:preloader:hidden', startEffects, { once: true });
    }
})();
