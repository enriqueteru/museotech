(() => {
    const scenes = [...document.querySelectorAll('[data-home-scroll-transition]')];
    const featureStacks = [...document.querySelectorAll('[data-home-feature-stack]')];
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const gsap = window.gsap;
    const scrollTrigger = window.ScrollTrigger;

    if ((!scenes.length && !featureStacks.length) || reducedMotion || !gsap || !scrollTrigger) {
        return;
    }

    gsap.registerPlugin(scrollTrigger);

    scenes.forEach((scene) => {
        const rows = [...scene.querySelectorAll('.home-scroll-transition__row')];

        rows.forEach((row) => {
            const blockCount = window.matchMedia('(max-width: 700px)').matches ? 10 : 16;
            for (let index = 0; index < blockCount; index++) {
                const block = document.createElement('span');
                block.className = 'home-scroll-transition__block';
                row.appendChild(block);
            }
        });

        const containers = [...scene.querySelectorAll('.home-scroll-transition__rows')];

        containers.forEach((container) => {
            const containerRows = [...container.querySelectorAll('.home-scroll-transition__row')];
            const isTop = container.classList.contains('home-scroll-transition__rows--top');

            containerRows.forEach((row, rowIndex) => {
                const blocks = [...row.querySelectorAll('.home-scroll-transition__block')];
                const order = gsap.utils.shuffle(blocks.map((_, index) => index));
                const rowDelay = 0.08 * (containerRows.length - rowIndex - 1);

                gsap.set(blocks, { opacity: isTop ? 1 : 0, clearProps: 'transform' });
                scrollTrigger.create({
                    trigger: scene,
                    start: 'top bottom',
                    end: 'bottom bottom',
                    onUpdate: (trigger) => {
                        const progress = Math.max(0, Math.min(1, trigger.progress - rowDelay));
                        blocks.forEach((block, index) => {
                            const offset = order.indexOf(index) / blocks.length;
                            const reveal = Math.max(0, Math.min(1, (progress - offset) * blocks.length));
                            block.style.opacity = String(isTop ? 1 - reveal : reveal);
                        });
                    },
                });
            });
        });
    });

    const featureStackMedia = gsap.matchMedia();
    featureStackMedia.add('(min-width: 761px)', () => {
        featureStacks.forEach((stack) => {
            const stage = stack.querySelector('.home-feature-stack__stage');
            const cards = [...stack.querySelectorAll('.home-feature-stack__stage > .home-feature-stack__card')];
            if (!stage || cards.length !== 2) return;

            const [podcastCard, repositoryCard] = cards;
            stack.classList.add('is-scroll-enhanced');
            gsap.set(podcastCard, { transformOrigin: 'center center' });
            gsap.set(repositoryCard, {
                yPercent: 108,
                scale: 0.96,
                visibility: 'hidden',
                transformOrigin: 'center center',
            });

            gsap.timeline({
                scrollTrigger: {
                    trigger: stack,
                    start: 'top top',
                    end: () => `+=${Math.round(window.innerHeight * (window.innerWidth <= 760 ? 1.9 : 1.7))}`,
                    pin: stage,
                    pinSpacing: true,
                    scrub: 0.6,
                    anticipatePin: 1,
                    invalidateOnRefresh: true,
                },
            })
                .to(podcastCard, {
                    y: -14,
                    scale: 0.96,
                    duration: 0.22,
                    ease: 'none',
                }, 0)
                .to(repositoryCard, {
                    yPercent: 0,
                    visibility: 'visible',
                    duration: 0.48,
                    ease: 'none',
                }, 0.12)
                .to(repositoryCard, {
                    top: 0,
                    right: 0,
                    bottom: 0,
                    left: 0,
                    scale: 1,
                    borderRadius: 0,
                    borderWidth: 0,
                    duration: 0.4,
                    ease: 'none',
                }, 0.6);
        });
        return () => featureStacks.forEach((stack) => stack.classList.remove('is-scroll-enhanced'));
    });

    document.addEventListener('museotech:keyboard-navigation', () => featureStackMedia.revert(), { once: true });
})();
