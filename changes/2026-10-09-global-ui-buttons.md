# Componente global de botones

Fecha: 2026-10-09

- Se creó `assets/css/ui-components.css` con el componente global `.ui-button`, basado en el estado activo de `.news-archive__filter`.
- El componente define la píldora negra corporativa, estados hover/focus, tamaños táctiles y variantes para filtros, controles compactos y recursos con metadatos.
- Se aplicó a los CTA de home, filtros y paginación del blog, filtros y acciones del repositorio, retornos y CTA de artículos/podcasts, descargas, recursos externos, copiar/exportar, enlaces profesionales del equipo y envío del formulario de contacto.
- La paginación nativa de WordPress comparte el mismo lenguaje visual.
- Los controles especializados —navegación global, cards completas, reproducción, carruseles, cerrar y selectores de equipo— conservan su geometría funcional.
- Las acciones del gestor de cookies adoptan también la píldora corporativa en banner y preferencias.
- Las flechas textuales se sustituyeron por los iconos minimalistas `arrow-right` y `external-link` de Lucide 1.53.0, seleccionados e incluidos como SVG inline sin cargar una librería en tiempo de ejecución.
- En móvil, la tabla de archivos del repositorio pasa a un bloque apilado para mostrar el nombre, tamaño y botón Descargar a ancho completo sin recortes.

## Verificación

- Auditoría de 50 plantillas PHP del tema para separar CTA de navegación, enlaces inline y controles especializados.
- PHP lint en las 13 plantillas/archivos PHP modificados.
- Chrome en 1440×900 y 390×844: 20 comprobaciones sobre 10 rutas, sin overflow, CTA sin clases pendientes, sin objetivos táctiles menores de 44 px y con la hoja global cargada en todas las rutas.
- Revisión visual de home, blog, repositorio, contacto, equipo y singles de artículo, podcast y repositorio.
- Iconos Lucide verificados en home y repositorio, escritorio y móvil: SVG de 16×16 px, `aria-hidden`, cero glifos de flecha y cero overflow.
- Banner de cookies móvil: tres acciones de 300×44 px, fondo negro, radio 999 px y cero overflow.
- `git diff --check` sin errores.
- `make test`, `make lint` y `make build` continúan bloqueados por la referencia inexistente a `plugins/plugin-skeleton/package.json`.
