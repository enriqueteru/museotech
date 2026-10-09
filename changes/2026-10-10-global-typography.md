# Tipografía global — 2026-10-10

- Unificada la escala tipográfica de todos los `h1`–`h6` con la de `.home-hero__headline`: 40.6–78.4 px en escritorio y 30.8–50.4 px en móvil, con la misma familia, peso, tracking y altura de línea.
- Unificado el texto de párrafo editorial con `.home-hero__description`: Satoshi, 14–17 px en escritorio y 15 px en móvil, con alturas de línea 1.55 y 1.5.
- Centralizados tamaños y alturas de línea como variables en `typography.css`; Home y banners reutilizan esas variables.
- Se conservan los estilos propios de textos funcionales breves (eyebrows/metadatos/footer) y del texto decorativo de la marquesina de Equipo.
- Los títulos de tarjetas de blog, podcast, repositorio y contenido relacionado usan una escala propia de 18–23 px (18–20 px en móvil), sin heredar el tamaño grande de los encabezados de sección.
- En `repository/:id`, el H1 largo se limita a 28–36 px (24–30 px en móvil); las cabeceras de panel quedan en 18 px, las de sección en 22 px (20 px en móvil) y los H2–H6 del contenido siguen una jerarquía compacta.
- `git diff --check` correcto. QA visual en Chromium pendiente: el navegador no pudo abrirse por permisos sobre `~/Library/Application Support/Google/Chrome/DevToolsActivePort`.
