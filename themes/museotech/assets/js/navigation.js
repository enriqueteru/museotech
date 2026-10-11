(() => {
    const menu = document.querySelector('#museotech-site-menu');
    const toggle = document.querySelector('.site-menu-toggle');
    const header = document.querySelector('.site-header');
    const logo = header?.querySelector('.custom-logo');
    const toggleLines = toggle?.querySelector('.site-menu-toggle__lines');

    if (!menu || !toggle || !header) {
        return;
    }

    const panels = [...menu.querySelectorAll('.site-menu__panel')];
    const items = [...menu.querySelectorAll('.site-menu__item')];
    const firstLink = menu.querySelector('.site-menu__list a') ?? menu.querySelector('a');
    const focusableSelector = 'a[href], button:not([disabled]), [tabindex]:not([tabindex="-1"])';
    const pageRegions = [...document.body.children].filter((element) =>
        !element.contains(menu) && !['SCRIPT', 'STYLE', 'LINK'].includes(element.tagName)
    );
    const originalInertState = new Map(pageRegions.map((element) => [element, element.inert]));
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    let isOpen = false;
    let isCompact = null;
    let openTimeline;
    let closeTimeline;

    const setHeaderSize = (compact, immediate = false) => {
        const isMobile = window.matchMedia('(max-width: 700px)').matches;
        const logoMaxWidth = compact ? (isMobile ? 162 : 230) : (isMobile ? 190 : 280);
        const values = {
            height: compact ? (isMobile ? 58 : 62) : (isMobile ? 72 : 84),
            paddingTop: compact ? (isMobile ? 4 : 6) : (isMobile ? 10 : 12),
            paddingBottom: compact ? (isMobile ? 4 : 6) : (isMobile ? 10 : 12),
        };

        header.classList.toggle('is-compact', compact);

        if (!window.gsap || immediate || reducedMotion.matches) {
            if (window.gsap) {
                window.gsap.set(header, values);
                if (logo) {
                    window.gsap.set(logo, { maxWidth: logoMaxWidth });
                }
                if (toggleLines) {
                    window.gsap.set(toggleLines, { scale: compact ? 0.92 : 1, transformOrigin: 'right center' });
                }
            } else {
                Object.assign(header.style, {
                    height: `${values.height}px`,
                    paddingTop: `${values.paddingTop}px`,
                    paddingBottom: `${values.paddingBottom}px`,
                });
                if (logo) {
                    logo.style.maxWidth = `${logoMaxWidth}px`;
                }
            }
            return;
        }

        const transition = { duration: 0.42, ease: 'power3.out', overwrite: 'auto' };
        window.gsap.to(header, { ...values, ...transition });
        if (logo) {
            window.gsap.to(logo, {
                maxWidth: logoMaxWidth,
                ...transition,
            });
        }
        if (toggleLines) {
            window.gsap.to(toggleLines, {
                scale: compact ? 0.92 : 1,
                transformOrigin: 'right center',
                ...transition,
            });
        }
    };

    const updateHeaderSize = (immediate = false) => {
        const compact = window.scrollY > 32;
        if (compact === isCompact && !immediate) {
            return;
        }

        isCompact = compact;
        setHeaderSize(compact, immediate);
    };

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

    const setExpanded = (expanded, keepMenuVisual = false) => {
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
        document.body.classList.toggle('has-open-menu', expanded || keepMenuVisual);
    };

    const buildOpenTimeline = () => {
        if (!window.gsap) {
            return null;
        }

        return window.gsap.timeline({ paused: true })
            .fromTo(panels, {
                clipPath: 'polygon(0% 0%, 100% 0%, 100% 0%, 0% 0%)',
            }, {
                duration: 0.85,
                clipPath: 'polygon(0% 0%, 100% 0%, 100% 100%, 0% 100%)',
                stagger: 0.065,
                ease: 'power3.inOut',
            })
            .fromTo(items, {
                opacity: 0,
                y: 18,
            }, {
                duration: 0.32,
                opacity: 1,
                y: 0,
                stagger: 0.055,
                ease: 'power2.out',
            }, '-=0.4');
    };

    const buildCloseTimeline = () => {
        if (!window.gsap) {
            return null;
        }

        return window.gsap.timeline({ paused: true, onComplete: setClosed })
            .to(items, {
                duration: 0.28,
                opacity: 0,
                y: 24,
                stagger: { each: 0.035, from: 'end' },
                ease: 'power2.in',
            })
            .to(panels, {
                duration: 0.62,
                clipPath: 'polygon(0% 100%, 100% 100%, 100% 100%, 0% 100%)',
                stagger: { each: 0.045, from: 'end' },
                ease: 'power3.inOut',
            }, '-=0.08');
    };

    const openMenu = () => {
        if (isOpen) {
            return;
        }

        menu.hidden = false;
        setExpanded(true);
        closeTimeline?.pause(0);
        openTimeline ??= buildOpenTimeline();

        if (openTimeline && !reducedMotion.matches) {
            openTimeline.progress(0).play();
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

        const hasAnimatedClose = Boolean(openTimeline && !reducedMotion.matches);
        setExpanded(false, hasAnimatedClose);

        if (hasAnimatedClose) {
            openTimeline.pause();
            closeTimeline ??= buildCloseTimeline();
            closeTimeline.progress(0).play();
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

    window.addEventListener('scroll', () => updateHeaderSize(), { passive: true });
    updateHeaderSize(true);

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
