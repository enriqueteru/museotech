# Logo en el menú abierto — 2026-10-08

- Añadido el logo configurado en WordPress al inicio del contenido del menú, antes de los enlaces; sin logo, se muestra el nombre del sitio.
- El logo negro se invierte a blanco sobre el fondo oscuro; el logo del encabezado se oculta mientras el menú está abierto para evitar duplicados.
- El foco inicial va al primer enlace de navegación, no al logo. Tamaño limitado para escritorio y móvil.
- Verificado en Chrome a 1440×900 y 390×844: logo cargado y visible, antes de la lista, sin solapamiento; `php -l`, `node --check` y `git diff --check` pasan.
