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

    const thumbnailMotion = (() => {
        const strip = page.querySelector('.team-page__thumbnails');
        const track = strip?.querySelector('.team-page__thumbnail-track');
        if (!track || !thumbnails.length) return null;

        const axes = ['x', 'y', 'rotation'];
        const bubbles = thumbnails.map((element) => ({
            element,
            value: { x: 0, y: 0, rotation: 0 },
            target: { x: 0, y: 0, rotation: 0 },
            velocity: { x: 0, y: 0, rotation: 0 },
        }));
        let selected = currentIndex;
        let hovered = -1;
        let size = 58;
        let frame = 0;
        let lastTime = 0;

        const render = (bubble) => {
            const { x, y, rotation } = bubble.value;
            bubble.element.style.transform = `translate3d(${x.toFixed(3)}px, ${y.toFixed(3)}px, 0) rotate(${rotation.toFixed(3)}deg)`;
        };

        const settle = () => {
            cancelAnimationFrame(frame);
            frame = 0;
            lastTime = 0;
            bubbles.forEach((bubble) => {
                axes.forEach((axis) => {
                    bubble.value[axis] = bubble.target[axis];
                    bubble.velocity[axis] = 0;
                });
                render(bubble);
            });
        };

        // Solve the damped spring at the elapsed frame time, preserving momentum
        // on rapid selections and the same duration on slower devices.
        const tick = (time) => {
            const delta = Math.max(0, (time - lastTime) / 1000);
            let moving = false;
            lastTime = time;

            bubbles.forEach((bubble, index) => {
                const distance = Math.abs(index - selected);
                const stiffness = 230 - Math.min(distance, 5) * 13;
                const damping = 0.76 * Math.sqrt(stiffness);
                const frequency = Math.sqrt(stiffness - damping * damping);
                const decay = Math.exp(-damping * delta);
                const cosine = Math.cos(frequency * delta);
                const sine = Math.sin(frequency * delta);
                axes.forEach((axis) => {
                    const displacement = bubble.value[axis] - bubble.target[axis];
                    const velocity = bubble.velocity[axis];
                    bubble.value[axis] = bubble.target[axis] + decay * (displacement * cosine + (velocity + damping * displacement) * sine / frequency);
                    bubble.velocity[axis] = decay * (velocity * cosine - (damping * velocity + stiffness * displacement) * sine / frequency);
                    const tolerance = 0.025;
                    if (Math.abs(bubble.target[axis] - bubble.value[axis]) > tolerance
                        || Math.abs(bubble.velocity[axis]) > tolerance * 10) {
                        moving = true;
                    } else {
                        bubble.value[axis] = bubble.target[axis];
                        bubble.velocity[axis] = 0;
                    }
                });
                render(bubble);
            });

            frame = moving ? requestAnimationFrame(tick) : 0;
            if (!moving) lastTime = 0;
        };

        const animate = () => {
            if (reducedMotion.matches) {
                settle();
            } else if (!frame && !document.hidden) {
                lastTime = performance.now();
                frame = requestAnimationFrame(tick);
            }
        };

        const layout = (immediate = false) => {
            const edge = 16;
            const stride = size * 0.68;
            const widthAvailable = strip.clientWidth;
            const centerY = (strip.clientHeight - size) / 2;
            // Leave a real gap around the enlarged circle; the others overlap.
            const selectedStride = size * 1.24;
            let x = edge;
            bubbles.forEach((bubble, index) => {
                if (index) x += index === selected || index - 1 === selected ? selectedStride : stride;
                const active = index === selected;
                const over = index === hovered;
                const neighbor = hovered >= 0 && Math.abs(index - hovered) === 1 && !active;
                bubble.target = {
                    x: x + (neighbor ? Math.sign(index - hovered) * 3 : 0),
                    y: centerY + (over ? -4 : 0),
                    rotation: over ? (index % 2 ? -1.2 : 1.2) : 0,
                };
                bubble.element.style.zIndex = String(active ? bubbles.length + 2 : over ? bubbles.length + 1 : bubbles.length - Math.abs(index - selected));
            });
            const width = x + size + edge;
            const offset = Math.max(0, (widthAvailable - width) / 2);
            bubbles.forEach((bubble) => { bubble.target.x += offset; });
            track.style.width = `${Math.max(width, widthAvailable)}px`;
            if (immediate) settle();
            else animate();
        };

        const revealSelected = (immediate = false) => {
            const x = bubbles[selected].target.x;
            const padding = 16 + size * 0.1;
            let left = strip.scrollLeft;
            if (x - padding < left) left = x - padding;
            else if (x + size + padding > left + strip.clientWidth) left = x + size + padding - strip.clientWidth;
            strip.scrollTo({ left: Math.max(0, left), behavior: immediate || reducedMotion.matches ? 'instant' : 'smooth' });
        };

        const resize = () => {
            size = parseFloat(getComputedStyle(strip).getPropertyValue('--team-thumbnail-size')) || 58;
            layout(true);
            revealSelected(true);
        };

        strip.classList.add('is-animated');
        resize();
        const resizeObserver = new ResizeObserver(resize);
        resizeObserver.observe(strip);

        strip.addEventListener('pointermove', (event) => {
            if (event.pointerType !== 'mouse' || !finePointer.matches) return;
            const index = thumbnails.indexOf(event.target.closest('.team-page__thumbnail'));
            if (hovered === index) return;
            hovered = index;
            layout();
        });
        strip.addEventListener('pointerleave', () => {
            if (hovered === -1) return;
            hovered = -1;
            layout();
        });
        reducedMotion.addEventListener('change', animate);
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                cancelAnimationFrame(frame);
                frame = 0;
                lastTime = 0;
            } else {
                animate();
            }
        });

        return {
            select(index) {
                if (index !== selected && !reducedMotion.matches) {
                    bubbles[index].velocity.y -= size * 0.35;
                }
                selected = index;
                hovered = -1;
                layout();
                revealSelected();
            },
        };
    })();

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
            thumbnail.tabIndex = active ? 0 : -1;
            if (active) {
                page.querySelector('#team-page-info')?.setAttribute('aria-labelledby', thumbnail.id);
                if (!thumbnailMotion) thumbnail.scrollIntoView({
                    behavior: reducedMotion.matches ? 'auto' : 'smooth',
                    block: 'nearest',
                    inline: 'nearest',
                });
            }
        });
        thumbnailMotion?.select(currentIndex);
    };

    const setClip = (element, clipPath, delay = 0, immediate = false) => {
        if (!element) {
            return;
        }

        window.gsap?.killTweensOf(element, 'clipPath');

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
        thumbnail.addEventListener('keydown', (event) => {
            const currentTabIndex = thumbnails.indexOf(thumbnail);
            let targetTabIndex = null;
            if (event.key === 'ArrowRight' || event.key === 'ArrowDown') {
                targetTabIndex = (currentTabIndex + 1) % thumbnails.length;
            } else if (event.key === 'ArrowLeft' || event.key === 'ArrowUp') {
                targetTabIndex = (currentTabIndex - 1 + thumbnails.length) % thumbnails.length;
            } else if (event.key === 'Home') {
                targetTabIndex = 0;
            } else if (event.key === 'End') {
                targetTabIndex = thumbnails.length - 1;
            }
            if (targetTabIndex === null) return;
            event.preventDefault();
            goTo(targetTabIndex);
            thumbnails[targetTabIndex].focus({ preventScroll: true });
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
