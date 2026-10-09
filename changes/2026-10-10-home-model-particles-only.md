# Modelos de Repositorio y Podcast: solo partículas

- Eliminados de ambos modelos el aura de luz, los anillos/órbitas lineales y las gemas flotantes; retirado también el filtro de sombra del canvas para evitar halos.
- Se conserva la nube de 240 partículas por modelo, con sus escalas y encuadre, y sin modificar los controles de giro ni `prefers-reduced-motion`.
- Verificación: `node --check`, aserciones de escena y `git diff --check` de los archivos tocados pasan; home y assets responden HTTP 200. El chequeo global señala espacios finales en `themes/museotech/template-parts/content/single.php:84,89` (fuera de este cambio). Chromium no pudo iniciar por permiso macOS a `DevToolsActivePort`, así que falta inspección visual WebGL.
