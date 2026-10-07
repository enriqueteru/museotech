(() => {
    const menu = document.querySelector('#museotech-site-menu');
    const toggle = document.querySelector('.site-menu-toggle');

    if (!menu || !toggle) {
        return;
    }

    const panels = [...menu.querySelectorAll('.site-menu__panel')];
    const items = [...menu.querySelectorAll('.site-menu__item')];
    const firstLink = menu.querySelector('a');
    const focusableSelector = 'a[href], button:not([disabled]), [tabindex]:not([tabindex="-1"])';
    const pageRegions = [...document.body.children].filter((element) =>
        !element.contains(menu) && !['SCRIPT', 'STYLE', 'LINK'].includes(element.tagName)
    );
    const originalInertState = new Map(pageRegions.map((element) => [element, element.inert]));
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    let isOpen = false;
    let timeline;

    const setClosed = () => {
        if (isOpen) {
            return;
        }

        menu.hidden = true;
        menu.inert = true;
        pageRegions.forEach((element) => {
            element.inert = originalInertState.get(element);
        });
        document.body.classList.remove('has-open-menu');
        toggle.focus({ preventScroll: true });
    };

    const setExpanded = (expanded) => {
        isOpen = expanded;
        toggle.setAttribute('aria-expanded', String(expanded));
        toggle.setAttribute(
            'aria-label',
            expanded ? 'Cerrar navegación' : 'Abrir navegación'
        );
        menu.setAttribute('aria-hidden', String(!expanded));
        menu.inert = !expanded;
        pageRegions.forEach((element) => {
            element.inert = expanded;
        });
        document.body.classList.toggle('has-open-menu', expanded);
    };

    const buildTimeline = () => {
        if (!window.gsap) {
            return null;
        }

        return window.gsap.timeline({
            paused: true,
            onReverseComplete: setClosed,
        })
            .to(panels, {
                duration: 0.85,
                clipPath: 'polygon(0% 0%, 100% 0%, 100% 100%, 0% 100%)',
                stagger: 0.065,
                ease: 'power3.inOut',
            })
            .to(items, {
                duration: 0.32,
                opacity: 1,
                y: 0,
                stagger: 0.055,
                ease: 'power2.out',
            }, '-=0.4');
    };

    const openMenu = () => {
        if (isOpen) {
            return;
        }

        menu.hidden = false;
        setExpanded(true);
        timeline ??= buildTimeline();

        if (timeline && !reducedMotion.matches) {
            timeline.play();
        } else {
            panels.forEach((panel) => {
                panel.style.clipPath = 'polygon(0% 0%, 100% 0%, 100% 100%, 0% 100%)';
            });
            items.forEach((item) => {
                item.style.opacity = '1';
                item.style.transform = 'translateY(0)';
            });
        }

        firstLink?.focus({ preventScroll: true });
    };

    const closeMenu = () => {
        if (!isOpen) {
            return;
        }

        setExpanded(false);

        if (timeline && !reducedMotion.matches) {
            timeline.reverse();
            return;
        }

        panels.forEach((panel) => {
            panel.style.clipPath = '';
        });
        items.forEach((item) => {
            item.style.opacity = '';
            item.style.transform = '';
        });
        setClosed();
    };

    toggle.addEventListener('click', () => {
        isOpen ? closeMenu() : openMenu();
    });

    menu.addEventListener('click', (event) => {
        if (event.target.closest('a')) {
            closeMenu();
        }
    });

    menu.addEventListener('keydown', (event) => {
        if (event.key !== 'Tab') {
            return;
        }

        const focusable = [...menu.querySelectorAll(focusableSelector)];
        const first = focusable[0];
        const last = focusable.at(-1);

        if (event.shiftKey && document.activeElement === first) {
            event.preventDefault();
            last?.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first?.focus();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && isOpen) {
            closeMenu();
        }
    });

    menu.inert = true;
})();