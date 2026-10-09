(() => {
    const content = document.querySelector('.podcast-single__content');
    const toc = document.querySelector('[data-podcast-single-toc]');

    if (!content || !toc) {
        return;
    }

    const slugify = (value) => value
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-|-$/g, '');

    const usedIds = new Set(['contenido-podcast']);
    const headings = Array.from(content.querySelectorAll('h2, h3'));

    headings.forEach((heading, index) => {
        let id = heading.id || slugify(heading.textContent) || `seccion-${index + 1}`;
        const baseId = id;
        let suffix = 2;

        while (usedIds.has(id)) {
            id = `${baseId}-${suffix}`;
            suffix += 1;
        }

        heading.id = id;
        usedIds.add(id);

        const item = document.createElement('li');
        const link = document.createElement('a');
        item.dataset.level = heading.tagName.slice(1);
        link.href = `#${id}`;
        link.textContent = heading.textContent;
        item.append(link);
        toc.append(item);
    });

    const tocLinks = Array.from(toc.querySelectorAll('a'));
    const targets = tocLinks.map((link) => document.querySelector(link.hash)).filter(Boolean);

    if (!('IntersectionObserver' in window) || targets.length === 0) {
        return;
    }

    const observer = new IntersectionObserver((entries) => {
        const visible = entries
            .filter((entry) => entry.isIntersecting)
            .sort((first, second) => first.boundingClientRect.top - second.boundingClientRect.top);

        if (!visible[0]) {
            return;
        }

        tocLinks.forEach((link) => {
            const active = link.hash === `#${visible[0].target.id}`;
            link.classList.toggle('is-active', active);
            if (active) {
                link.setAttribute('aria-current', 'location');
            } else {
                link.removeAttribute('aria-current');
            }
        });
    }, { rootMargin: '-18% 0px -68% 0px', threshold: 0 });

    targets.forEach((target) => observer.observe(target));
})();

(() => {
    const player = document.querySelector('[data-podcast-single-player]');

    if (!player) {
        return;
    }

    const audio = player.querySelector('[data-podcast-single-audio]');
    const playButton = player.querySelector('[data-podcast-single-play]');
    const progress = player.querySelector('[data-podcast-single-progress]');
    const currentTime = player.querySelector('[data-podcast-current-time]');
    const duration = player.querySelector('[data-podcast-duration]');

    if (!audio || !playButton || !progress || !currentTime || !duration) {
        return;
    }

    const formatTime = (seconds) => {
        if (!Number.isFinite(seconds)) {
            return '--:--';
        }

        const minutes = Math.floor(seconds / 60);
        const remainingSeconds = Math.floor(seconds % 60).toString().padStart(2, '0');
        return `${minutes}:${remainingSeconds}`;
    };

    const setPlaying = (isPlaying) => {
        playButton.setAttribute('aria-pressed', isPlaying ? 'true' : 'false');
        playButton.setAttribute('aria-label', isPlaying ? playButton.dataset.pauseLabel : playButton.dataset.playLabel);
    };

    const updateProgress = () => {
        const percentage = audio.duration ? (audio.currentTime / audio.duration) * 100 : 0;
        progress.value = String(percentage);
        player.style.setProperty('--audio-progress', `${percentage}%`);
        currentTime.textContent = formatTime(audio.currentTime);
    };

    const updateDuration = () => {
        duration.textContent = formatTime(audio.duration);
    };

    playButton.addEventListener('click', async () => {
        if (!audio.paused) {
            audio.pause();
            return;
        }

        try {
            await audio.play();
        } catch (error) {
            setPlaying(false);
        }
    });

    progress.addEventListener('input', () => {
        if (audio.duration) {
            audio.currentTime = (Number(progress.value) / 100) * audio.duration;
            updateProgress();
        }
    });

    audio.addEventListener('loadedmetadata', () => {
        updateDuration();
        updateProgress();
    });
    audio.addEventListener('durationchange', updateDuration);
    audio.addEventListener('timeupdate', updateProgress);
    audio.addEventListener('play', () => setPlaying(true));
    audio.addEventListener('pause', () => setPlaying(false));
    audio.addEventListener('ended', () => {
        audio.currentTime = 0;
        setPlaying(false);
        updateProgress();
    });

    if (audio.readyState >= 1) {
        updateDuration();
        updateProgress();
    }
})();
