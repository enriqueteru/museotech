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
    let transitionImage = null;
    let titleLines = [];

    if (!members.length || !previous || !next || !name || !role) {
        return;
    }

    const visibleClip = 'polygon(0% 100%, 100% 100%, 100% 0%, 0% 0%)';
    const hiddenClip = 'polygon(0 100%, 100% 100%, 100% 100%, 0% 100%)';
    const fullDialogClip = 'inset(0px 0px 0px 0px)';
    const hiddenDialogClip = 'inset(100% 0px 0px 0px)';
    const getProfileRevealElements = (panel) => [
        panel?.querySelector('.team-profile-dialog__meta'),
        ...(panel?.querySelectorAll('.team-profile-dialog__bio p') || []),
        ...(panel?.querySelectorAll('.team-profile-dialog__resources a') || []),
        panel?.querySelector('.team-profile-dialog__link'),
    ].filter(Boolean);

    profilePanels.forEach((panel) => {
        getProfileRevealElements(panel).forEach((element) => {
            element.classList.add('team-profile-dialog__reveal');
        });
    });

    const splitProfileTitle = (heading) => {
        if (!heading) return [];
        const original = heading.dataset.revealText || heading.textContent.trim().replace(/\s+/g, ' ');
        heading.dataset.revealText = original;
        heading.setAttribute('aria-label', original);
        const words = original.split(' ').map((word) => {
            const span = document.createElement('span');
            span.className = 'team-profile-dialog__measure-word';
            span.textContent = word;
            return span;
        });
        heading.replaceChildren();
        words.forEach((word, index) => {
            heading.append(word);
            if (index < words.length - 1) heading.append(document.createTextNode(' '));
        });

        const groups = [];
        words.forEach((word) => {
            const top = word.getBoundingClientRect().top;
            let group = groups[groups.length - 1];
            if (!group || Math.abs(group.top - top) > 1) {
                group = { top, words: [] };
                groups.push(group);
            }
            group.words.push(word.textContent);
        });

        heading.replaceChildren();
        return groups.map((group) => {
            const mask = document.createElement('span');
            mask.className = 'team-profile-dialog__reveal-mask';
            mask.setAttribute('aria-hidden', 'true');
            const line = document.createElement('span');
            line.className = 'team-profile-dialog__reveal-line';
            line.textContent = group.words.join(' ');
            mask.append(line);
            heading.append(mask);
            return line;
        });
    };

    const imageRect = (image) => image?.getBoundingClientRect();
    const clipAround = (rect) => {
        const dialogRect = profileDialog.getBoundingClientRect();
        return `inset(${Math.max(0, rect.top - dialogRect.top)}px ${Math.max(0, dialogRect.right - rect.right)}px ${Math.max(0, dialogRect.bottom - rect.bottom)}px ${Math.max(0, rect.left - dialogRect.left)}px)`;
    };
    const makeTransitionImage = (image, rect) => {
        const clone = image.cloneNode(false);
        clone.className = 'team-profile-dialog__transition-image';
        clone.removeAttribute('srcset');
        clone.removeAttribute('sizes');
        clone.removeAttribute('loading');
        clone.src = image.currentSrc || image.src;
        clone.alt = '';
        clone.setAttribute('aria-hidden', 'true');
        clone.style.objectPosition = getComputedStyle(image).objectPosition;
        Object.assign(clone.style, {
            left: `${rect.left}px`,
            top: `${rect.top}px`,
            width: `${rect.width}px`,
            height: `${rect.height}px`,
        });
        profileDialog.append(clone);
        transitionImage = clone;
        return clone;
    };
    const removeTransitionImage = () => {
        transitionImage?.remove();
        transitionImage = null;
    };

    const thumbnailMotion = (() => {
        const strip = page.querySelector('.team-page__thumbnails');
        const track = strip?.querySelector('.team-page__thumbnail-track');
        if (!track || !thumbnails.length) return null;

        const axes = ['x', 'y'];
        const bubbles = thumbnails.map((element) => ({
            element,
            value: { x: 0, y: 0 },
            target: { x: 0, y: 0 },
            velocity: { x: 0, y: 0 },
        }));
        let selected = currentIndex;
        let size = 58;
        let frame = 0;
        let lastTime = 0;

        const render = (bubble) => {
            const { x, y } = bubble.value;
            bubble.element.style.transform = `translate3d(${x.toFixed(3)}px, ${y.toFixed(3)}px, 0)`;
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
            const stride = size + 8;
            const widthAvailable = strip.clientWidth;
            const centerY = (strip.clientHeight - size) / 2;
            const selectedStride = size + 14;
            let x = edge;
            bubbles.forEach((bubble, index) => {
                if (index) x += index === selected || index - 1 === selected ? selectedStride : stride;
                const active = index === selected;
                bubble.target = {
                    x,
                    y: centerY,
                };
                bubble.element.style.zIndex = String(active ? 2 : 1);
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
                selected = index;
                layout();
                revealSelected();
            },
        };
    })();

    const updateInfo = () => {
        name.textContent = members[currentIndex].name;
        role.textContent = members[currentIndex].role;
        const selectedImage = images[currentIndex]?.querySelector('img');
        const selectedProfileImage = profilePanels[currentIndex]?.querySelector('.team-profile-dialog__image img');
        if (selectedImage) selectedImage.loading = 'eager';
        if (selectedProfileImage) selectedProfileImage.loading = 'eager';
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

        const sourceImage = images[currentIndex]?.querySelector('img');
        const sourceRect = imageRect(sourceImage);
        profilePanels.forEach((panel, index) => {
            panel.hidden = index !== currentIndex;
        });
        profileDialog.showModal();
        profileClosing = false;

        const activePanel = profilePanels[currentIndex];
        const activeImage = activePanel?.querySelector('.team-profile-dialog__image img');
        if (activeImage) activeImage.loading = 'eager';
        const targetRect = imageRect(activeImage);
        const heading = activePanel?.querySelector('h3');
        const fades = getProfileRevealElements(activePanel);
        profileAnimation?.kill();

        if (window.gsap && !reducedMotion.matches && profileShell) {
            const canMorph = sourceRect && targetRect && sourceRect.width && targetRect.width;
            titleLines = splitProfileTitle(heading);
            window.gsap.set(profileShell, { clipPath: canMorph ? clipAround(sourceRect) : hiddenDialogClip });
            window.gsap.set(titleLines, { yPercent: 110 });
            window.gsap.set(fades, { autoAlpha: 0, y: 12 });
            window.gsap.set(profileClose, { opacity: 0, scale: 0.88 });
            if (canMorph) {
                activeImage.style.visibility = 'hidden';
                makeTransitionImage(sourceImage, sourceRect);
            }

            profileAnimation = window.gsap.timeline({
                onComplete: () => {
                    if (activeImage) activeImage.style.visibility = '';
                    removeTransitionImage();
                    window.gsap.set(profileShell, { clearProps: 'clipPath' });
                    window.gsap.set(titleLines, { clearProps: 'transform' });
                    window.gsap.set(fades, { clearProps: 'transform,opacity,visibility' });
                    window.gsap.set(profileClose, { clearProps: 'transform,opacity' });
                    profileAnimation = null;
                },
            });
            profileAnimation
                .to(profileShell, { clipPath: fullDialogClip, duration: 0.82, ease: 'power2.inOut' }, 0)
                .to(titleLines, {
                    yPercent: 0,
                    duration: 0.88,
                    ease: 'power3.out',
                    stagger: 0.1,
                }, 0.38)
                .to(fades, {
                    autoAlpha: 1,
                    y: 0,
                    duration: 0.68,
                    stagger: 0.1,
                    ease: 'power2.out',
                }, 0.5)
                .to(profileClose, { opacity: 1, scale: 1, duration: 0.34, ease: 'power2.out' }, 0.5);
            if (canMorph) {
                profileAnimation.to(transitionImage, {
                    left: targetRect.left,
                    top: targetRect.top,
                    width: targetRect.width,
                    height: targetRect.height,
                    duration: 0.82,
                    ease: 'power2.inOut',
                }, 0);
            }
        }

        profileClose?.focus();
    };

    const closeProfile = () => {
        if (!profileDialog?.open || profileClosing) {
            return;
        }

        const finishClosing = () => {
            const activeImage = profilePanels[currentIndex]?.querySelector('.team-profile-dialog__image img');
            if (activeImage) activeImage.style.visibility = '';
            removeTransitionImage();
            profileDialog.close();
            profileClosing = false;
            profileAnimation = null;
            if (window.gsap) {
                window.gsap.set(profileShell, { clearProps: 'clipPath' });
                window.gsap.set(titleLines, { clearProps: 'transform' });
                window.gsap.set(getProfileRevealElements(profilePanels[currentIndex]), { clearProps: 'transform,opacity,visibility' });
                window.gsap.set(profileClose, { clearProps: 'transform,opacity' });
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
        const sourceImage = images[currentIndex]?.querySelector('img');
        const sourceRect = imageRect(sourceImage);
        const targetRect = imageRect(activeImage);
        const fades = getProfileRevealElements(activePanel);
        const canMorph = sourceRect && targetRect && sourceRect.width && targetRect.width;
        if (canMorph && !transitionImage) makeTransitionImage(activeImage, targetRect);
        if (canMorph) activeImage.style.visibility = 'hidden';
        if (!profileShell.style.clipPath) window.gsap.set(profileShell, { clipPath: fullDialogClip });
        profileAnimation = window.gsap.timeline({ onComplete: finishClosing });
        profileAnimation
            .to(titleLines, {
                yPercent: -110,
                duration: 0.42,
                ease: 'power2.in',
                stagger: 0.035,
            }, 0)
            .to(fades, {
                autoAlpha: 0,
                y: -12,
                duration: 0.36,
                ease: 'power2.in',
                stagger: 0.025,
            }, 0)
            .to(profileShell, { clipPath: canMorph ? clipAround(sourceRect) : hiddenDialogClip, duration: 0.76, ease: 'power2.inOut' }, 0)
            .to(profileClose, { opacity: 0, scale: 0.9, duration: 0.32, ease: 'power2.in' }, 0);
        if (canMorph) {
            profileAnimation.to(transitionImage, {
                left: sourceRect.left,
                top: sourceRect.top,
                width: sourceRect.width,
                height: sourceRect.height,
                duration: 0.76,
                ease: 'power2.inOut',
            }, 0);
        }
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
