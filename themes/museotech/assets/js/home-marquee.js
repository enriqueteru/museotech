(() => {
    const marquees = document.querySelectorAll('[data-home-marquee]');

    if (!marquees.length || typeof window.gsap === 'undefined') {
        return;
    }

    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    let previousScroll = window.scrollY;
    let direction = 1;
    let scrollFrame = 0;

    marquees.forEach((marquee) => {
        const track = marquee.querySelector('[data-home-marquee-track]');
        const parts = marquee.querySelectorAll('[data-home-marquee-part]');
        const arrows = marquee.querySelectorAll('[data-home-marquee-arrow]');

        if (!track || !parts.length) {
            return;
        }

        window.gsap.set(track, { xPercent: 0 });

        if (reducedMotion) {
            return;
        }

        const tween = window.gsap.to(track, {
            xPercent: -50,
            repeat: -1,
            duration: 28,
            ease: 'none',
            paused: true,
        }).totalProgress(0.5);

        tween.timeScale(direction);

        const observer = new IntersectionObserver(([entry]) => {
            if (entry.isIntersecting) {
                tween.play();
            } else {
                tween.pause();
            }
        }, { threshold: 0.01 });

        observer.observe(marquee);

        const updateDirection = () => {
            const nextDirection = window.scrollY >= previousScroll ? 1 : -1;
            previousScroll = window.scrollY;
            scrollFrame = 0;

            if (nextDirection === direction) {
                return;
            }

            direction = nextDirection;
            window.gsap.to(tween, {
                timeScale: direction,
                duration: 0.35,
                ease: 'power2.out',
                overwrite: true,
            });
            arrows.forEach((arrow) => {
                arrow.classList.toggle('is-reversed', direction < 0);
            });
        };

        window.addEventListener('scroll', () => {
            if (!scrollFrame) {
                scrollFrame = window.requestAnimationFrame(updateDirection);
            }
        }, { passive: true });

        window.addEventListener('pagehide', () => {
            observer.disconnect();
            tween.kill();
            if (scrollFrame) {
                window.cancelAnimationFrame(scrollFrame);
            }
        }, { once: true });
    });
})();
