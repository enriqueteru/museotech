(() => {
    const players = Array.from(document.querySelectorAll('.podcast-archive-card')).map((card) => ({
        card,
        button: card.querySelector('[data-podcast-play]'),
        audio: card.querySelector('[data-podcast-audio]'),
    })).filter(({ button, audio }) => button && audio);

    if (players.length === 0) {
        return;
    }

    const setPlaying = (player, isPlaying) => {
        player.card.classList.toggle('is-playing', isPlaying);
        player.button.setAttribute('aria-pressed', isPlaying ? 'true' : 'false');
        player.button.setAttribute('aria-label', isPlaying ? player.button.dataset.pauseLabel : player.button.dataset.playLabel);
    };

    const pausePlayer = (player) => {
        player.audio.pause();
        setPlaying(player, false);
    };

    players.forEach((player) => {
        player.button.addEventListener('click', async () => {
            if (!player.audio.paused) {
                pausePlayer(player);
                return;
            }

            players.forEach((otherPlayer) => {
                if (otherPlayer !== player) {
                    pausePlayer(otherPlayer);
                }
            });

            player.button.classList.add('is-loading');
            try {
                await player.audio.play();
                setPlaying(player, true);
            } catch (error) {
                setPlaying(player, false);
            } finally {
                player.button.classList.remove('is-loading');
            }
        });

        player.audio.addEventListener('ended', () => setPlaying(player, false));
        player.audio.addEventListener('pause', () => setPlaying(player, false));
        player.audio.addEventListener('play', () => setPlaying(player, true));
    });
})();
