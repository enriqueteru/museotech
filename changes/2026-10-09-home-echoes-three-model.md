# Modelo Echoes of Marble en la home

Fecha: 2026-10-09

- Se confirmó que el archivo y los singles del repositorio no cargan Three.js ni modelos GLB.
- Se añadió un espacio 3D independiente en la home, después de Objetivos y antes de «Voces que conectan».
- La escena usa la copia optimizada `assets/models/echoes-of-marble.glb` del archivo `Meshy_AI_Echoes_of_Marble_1009013053_generate.glb`.
- Se reutiliza el cargador Three.js existente con carga diferida, Meshopt, giro, arrastre, teclado y respeto por `prefers-reduced-motion`.
- El módulo mantiene cero overflow horizontal y adapta escala y composición a escritorio y móvil.

## Verificación

- PHP lint: `front-page.php`, `home-echoes.php` y `inc/theme-setup.php`.
- JavaScript syntax: `node --check assets/js/home-hero-model.js`.
- Chrome/WebGL: estado `ready`, canvas presente, orden correcto y cero overflow en 1440×900 y 390×844.
- Revisión visual real en escritorio y móvil.
- `git diff --check` sin errores.
- `make test`, `make lint` y `make build` permanecen bloqueados por la referencia inexistente a `plugins/plugin-skeleton/package.json`.
