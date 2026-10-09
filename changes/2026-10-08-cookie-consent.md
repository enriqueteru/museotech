# Gestor de consentimiento de cookies

- Integrada localmente la versión 1.5.3 de `@solvenza/cookies-compliance` desde el plugin activo `museotech-core`.
- Se añade un banner en español con aceptación, rechazo y configuración, enlazado a las políticas existentes; aceptar y rechazar tienen la misma prominencia visual.
- Se habilita el botón flotante permanente después de la primera elección y la señal GPC para rechazar categorías opcionales.
- Se corrigió en la copia local un ciclo infinito de `solvenza:show` presente en la versión npm 1.5.3, que impedía la carga del banner en la primera visita.
- La configuración solo declara finalidades generales; no inventa proveedores. La SDK requiere marcar explícitamente scripts, iframes e imágenes opcionales para bloquearlos.
- Pendiente para una afirmación de cumplimiento completa: inventariar los servicios/cookies reales y revisar los recursos externos existentes (incluidos GSAP/ScrollTrigger servidos desde CDN).
