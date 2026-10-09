(() => {
    const announce = (element, message) => {
        if (element) {
            element.textContent = message;
        }
    };

    const copyText = async (text) => {
        if (navigator.clipboard && window.isSecureContext) {
            await navigator.clipboard.writeText(text);
            return;
        }

        const field = document.createElement('textarea');
        field.className = 'repository-single__copy-fallback';
        field.value = text;
        field.setAttribute('readonly', '');
        field.setAttribute('aria-hidden', 'true');
        document.body.append(field);
        field.select();
        const copied = document.execCommand('copy');
        field.remove();

        if (!copied) {
            throw new Error('Clipboard copy failed');
        }
    };

    document.querySelectorAll('[data-repository-copy]').forEach((button) => {
        button.addEventListener('click', async () => {
            const citation = document.getElementById(button.dataset.copyTarget);
            const status = document.getElementById('repository-single-copy-status');

            if (!citation) {
                announce(status, 'No se encontró la cita.');
                return;
            }

            try {
                await copyText(citation.textContent.trim());
                announce(status, 'Cita copiada.');
            } catch (error) {
                announce(status, 'No se pudo copiar; puedes seleccionar el texto de la cita.');
            }
        });
    });

    document.querySelectorAll('[data-repository-export]').forEach((button) => {
        button.addEventListener('click', () => {
            const dataElement = document.getElementById(button.dataset.exportTarget);
            const status = document.getElementById('repository-single-export-status');

            if (!dataElement) {
                announce(status, 'No se encontraron metadatos para exportar.');
                return;
            }

            try {
                const data = JSON.parse(dataElement.textContent);
                const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json;charset=utf-8' });
                const objectUrl = URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = objectUrl;
                link.download = button.dataset.exportFilename || 'museotech-repositorio.json';
                link.hidden = true;
                document.body.append(link);
                link.click();
                link.remove();
                window.setTimeout(() => URL.revokeObjectURL(objectUrl), 1000);
                announce(status, 'Descarga JSON iniciada.');
            } catch (error) {
                announce(status, 'No se pudo generar el archivo JSON.');
            }
        });
    });
})();
