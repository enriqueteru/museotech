# Identidad corporativa en páginas internas

Fecha: 2026-10-09

## Cambios

- Centralizados los colores de tinta, texto, fondo, superficie y borde en tokens compartidos, conservando GT America para titulares y Satoshi para descripciones.
- Unificada la jerarquía y colocación de los banners de blog, repositorio, colaboradores y podcasts con la home, con ajustes responsive.
- Llevadas las páginas de archivo y los singles de blog, repositorio y podcast a una paleta blanca, negra y neutra; se neutralizaron también las páginas de equipo y legales.
- Corregido el `box-sizing` del archivo de repositorio, que causaba desbordamiento horizontal en desktop y móvil.
- Contacto y la portada no se rediseñaron.

## Verificación

- QA en Chrome real: home, blog, repositorio, colaboradores, podcasts, contacto y singles de blog, repositorio y podcast; 1440×900 y 390×844. Resultado: 18/18 estados sin overflow horizontal; los cuatro archivos usan GT America en títulos y negro para texto principal. Contacto no carga los estilos compartidos de banners.
- `php -l themes/museotech/inc/theme-setup.php`: sin errores.
- `git diff --check`: correcto.
- `make test`, `make lint` y `make build`: bloqueados porque `Makefile` apunta a `plugins/plugin-skeleton`, que no existe en el repositorio (`package.json` ausente).
