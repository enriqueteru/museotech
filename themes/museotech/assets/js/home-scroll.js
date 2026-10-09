(() => {
    const scenes = [...document.querySelectorAll('[data-home-scroll-transition]')];
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (!scenes.length || reducedMotion || !window.gsap || !window.ScrollTrigger) {
        return;
    }

    window.gsap.registerPlugin(window.ScrollTrigger);

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
                const order = window.gsap.utils.shuffle(blocks.map((_, index) => index));
                const rowDelay = 0.08 * (containerRows.length - rowIndex - 1);

                window.gsap.set(blocks, { opacity: isTop ? 1 : 0, clearProps: 'transform' });
                window.ScrollTrigger.create({
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
})();