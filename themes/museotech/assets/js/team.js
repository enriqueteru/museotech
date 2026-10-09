(() => {
    const page = document.querySelector('.team-page');
    const dataNode = page?.querySelector('.team-page__data');

    if (!page || !dataNode) {
        return;
    }

    const members = JSON.parse(dataNode.textContent || '[]');
    const marquees = [...page.querySelectorAll('[data-team-slide]')];
    const images = [...page.querySelectorAll('[data-team-image]')];
    const thumbnails = [...page.querySelectorAll('.team-page__thumbnail')];
    const name = page.querySelector('.team-page__name');
    const role = page.querySelector('.team-page__role');
    const previous = page.querySelector('.team-page__control--previous');
    const next = page.querySelector('.team-page__control--next');
    const cursor = page.querySelector('.team-page__cursor');
    const profileTrigger = page.querySelector('.team-page__profile-trigger');
    const profileDialog = page.querySelector('.team-profile-dialog');
    const profileShell = page.querySelector('.team-profile-dialog__shell');
    const profileClose = page.querySelector('.team-profile-dialog__close');
    const profilePanels = [...page.querySelectorAll('[data-profile-panel]')];
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const finePointer = window.matchMedia('(hover: hover) and (pointer: fine)');
    let currentIndex = 0;
    let profileAnimation = null;
    let profileClosing = false;

    if (!members.length || !previous || !next || !name || !role) {
        return;
    }

    const visibleClip = 'polygon(0% 100%, 100% 100%, 100% 0%, 0% 0%)';
    const hiddenClip = 'polygon(0 100%, 100% 100%, 100% 100%, 0% 100%)';
    const hiddenTextClip = 'inset(100% 0% 0% 0%)';
    const visibleTextClip = 'inset(0% 0% 0% 0%)';
    const exitingTextClip = 'inset(0% 0% 100% 0%)';

    const getProfileRevealElements = (panel) => [
        panel?.querySelector('.team-profile-dialog__meta'),
        panel?.querySelector('h3'),
        ...(panel?.querySelectorAll('.team-profile-dialog__bio p') || []),
        ...(panel?.querySelectorAll('.team-profile-dialog__resources a') || []),
        panel?.querySelector('.team-profile-dialog__link'),
    ].filter(Boolean);

    profilePanels.forEach((panel) => {
        getProfileRevealElements(panel).forEach((element) => {
            element.classList.add('team-profile-dialog__reveal');
        });
    });

    const updateInfo = () => {
        name.textContent = members[currentIndex].name;
        role.textContent = members[currentIndex].role;
        previous.disabled = currentIndex === 0;
        next.disabled = currentIndex === members.length - 1;
        profileTrigger?.setAttribute(
            'aria-label',
            `${profileTrigger.dataset.profileLabel} ${members[currentIndex].name}`
        );
        thumbnails.forEach((thumbnail, index) => {
            const active = index === currentIndex;
            thumbnail.classList.toggle('is-active', active);
            thumbnail.setAttribute('aria-selected', String(active));
            if (active) {
                thumbnail.scrollIntoView({
                    behavior: reducedMotion.matches ? 'auto' : 'smooth',
                    block: 'nearest',
                    inline: 'nearest',
                });
            }
        });
    };

    const setClip = (element, clipPath, delay = 0, immediate = false) => {
        if (!element) {
            return;
        }

        if (window.gsap && immediate) {
            window.gsap.set(element, { clipPath });
            return;
        }

        if (window.gsap && !reducedMotion.matches) {
            window.gsap.to(element, {
                clipPath,
                duration: 1,
                ease: 'power4.out',
                delay,
            });
            return;
        }

        element.style.clipPath = clipPath;
    };

    const goTo = (targetIndex) => {
        const boundedTarget = Math.max(0, Math.min(members.length - 1, targetIndex));
        if (boundedTarget === currentIndex) {
            return;
        }

        if (boundedTarget > currentIndex) {
            for (let index = currentIndex + 1; index < boundedTarget; index += 1) {
                setClip(marquees[index], visibleClip, 0, true);
                setClip(images[index], visibleClip, 0, true);
            }
            setClip(marquees[boundedTarget], visibleClip, 0.3);
            setClip(images[boundedTarget], visibleClip);
        } else {
            setClip(marquees[currentIndex], hiddenClip, 0.3);
            setClip(images[currentIndex], hiddenClip);
            for (let index = currentIndex - 1; index > boundedTarget; index -= 1) {
                setClip(marquees[index], hiddenClip, 0, true);
                setClip(images[index], hiddenClip, 0, true);
            }
        }

        currentIndex = boundedTarget;
        updateInfo();
    };

    const showNext = () => goTo(currentIndex + 1);
    const showPrevious = () => goTo(currentIndex - 1);

    const openProfile = () => {
        if (!profileDialog || profileDialog.open) {
            return;
        }

        profilePanels.forEach((panel, index) => {
            panel.hidden = index !== currentIndex;
        });
        profileDialog.showModal();
        profileClosing = false;

        const activePanel = profilePanels[currentIndex];
        const activeImage = activePanel?.querySelector('.team-profile-dialog__image img');
        const activeContent = activePanel?.querySelector('.team-profile-dialog__content');
        const revealElements = getProfileRevealElements(activePanel);
        profileAnimation?.kill();

        if (window.gsap && !reducedMotion.matches && profileShell) {
            window.gsap.set(profileShell, { clipPath: hiddenClip });
            window.gsap.set(activeImage, { scale: 1.035 });
            window.gsap.set(activeContent, { opacity: 1, y: 0 });
            window.gsap.set(revealElements, { clipPath: hiddenTextClip, y: 14 });
            window.gsap.set(profileClose, { opacity: 0, scale: 0.88 });

            profileAnimation = window.gsap.timeline({
                onComplete: () => {
                    window.gsap.set([activeImage, activeContent, profileClose], { clearProps: 'transform,opacity' });
                    window.gsap.set(revealElements, { clearProps: 'transform' });
                    profileAnimation = null;
                },
            });
            profileAnimation
                .to(profileShell, { clipPath: visibleClip, duration: 0.72, ease: 'power4.out' })
                .to(activeImage, { scale: 1, duration: 0.9, ease: 'power3.out' }, 0)
                .to(revealElements, {
                    clipPath: visibleTextClip,
                    y: 0,
                    duration: 0.56,
                    ease: 'power3.out',
                    stagger: 0.045,
                }, 0.14)
                .to(profileClose, { opacity: 1, scale: 1, duration: 0.34, ease: 'power2.out' }, 0.28);
        }

        profileClose?.focus();
    };

    const closeProfile = () => {
        if (!profileDialog?.open || profileClosing) {
            return;
        }

        const finishClosing = () => {
            profileDialog.close();
            profileClosing = false;
            profileAnimation = null;
            if (window.gsap) {
                window.gsap.set(profileShell, { clearProps: 'clipPath' });
            }
            profileTrigger?.focus({ preventScroll: true });
        };

        if (!window.gsap || reducedMotion.matches || !profileShell) {
            finishClosing();
            return;
        }

        profileClosing = true;
        profileAnimation?.kill();
        const activePanel = profilePanels[currentIndex];
        const activeImage = activePanel?.querySelector('.team-profile-dialog__image img');
        const revealElements = getProfileRevealElements(activePanel);
        profileAnimation = window.gsap.timeline({ onComplete: finishClosing });
        profileAnimation
            .to(revealElements, {
                clipPath: exitingTextClip,
                y: -8,
                duration: 0.36,
                ease: 'power2.in',
                stagger: 0.025,
            })
            .to(profileShell, { clipPath: hiddenClip, duration: 0.68, ease: 'power3.inOut' }, 0.08)
            .to(activeImage, { scale: 1.02, duration: 0.68, ease: 'power3.inOut' }, 0.08)
            .to(profileClose, { opacity: 0, scale: 0.9, duration: 0.32, ease: 'power2.in' }, 0.04);
    };

    const updateCursor = (clientX) => {
        if (!cursor || !finePointer.matches) {
            return;
        }

        const pointsRight = clientX > window.innerWidth / 2;
        const canMove = pointsRight ? currentIndex < members.length - 1 : currentIndex > 0;
        cursor.hidden = !canMove;
        cursor.classList.toggle('is-right', pointsRight);
    };

    previous.addEventListener('click', showPrevious);
    next.addEventListener('click', showNext);
    thumbnails.forEach((thumbnail) => {
        thumbnail.addEventListener('click', () => {
            goTo(Number(thumbnail.dataset.teamTarget) - 1);
        });
    });
    profileTrigger?.addEventListener('click', openProfile);
    profileClose?.addEventListener('click', closeProfile);
    profileDialog?.addEventListener('cancel', (event) => {
        event.preventDefault();
        closeProfile();
    });
    profileDialog?.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            event.preventDefault();
            closeProfile();
        }
    });
    profileDialog?.addEventListener('click', (event) => {
        if (event.target === profileDialog) {
            closeProfile();
        }
    });
    [profileTrigger, ...thumbnails].filter(Boolean).forEach((control) => {
        control.addEventListener('pointerenter', () => page.classList.add('is-profile-hover'));
        control.addEventListener('pointerleave', () => page.classList.remove('is-profile-hover'));
    });

    document.addEventListener('keydown', (event) => {
        if (profileDialog?.open) {
            return;
        }
        if (event.key === 'ArrowRight') {
            showNext();
        }
        if (event.key === 'ArrowLeft') {
            showPrevious();
        }
    });

    document.addEventListener('mousemove', (event) => {
        if (!cursor || !finePointer.matches) {
            return;
        }

        const x = event.clientX - cursor.offsetWidth / 2;
        const y = event.clientY - cursor.offsetHeight / 2;
        if (window.gsap && !reducedMotion.matches) {
            window.gsap.to(cursor, { x, y, duration: 1, ease: 'power3.out' });
        } else {
            cursor.style.transform = `translate(${x}px, ${y}px)`;
        }
        updateCursor(event.clientX);
    });

    updateInfo();
})();