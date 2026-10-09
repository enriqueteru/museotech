# Modelo 3D en el Hero — 2026-10-09

- Integrado el GLB optimizado en `home-hero__content`, detrás del titular y la descripción; al cargar aparece desde arriba y desciende hasta su posición actual.
- El giro acompaña el scroll mediante GSAP ScrollTrigger, con una deriva de reposo sutil. La imagen configurada en `hero_image` se trasladó desde el Hero al final de `home-podcast__content`, después del CTA.
- El modelo respeta `prefers-reduced-motion` (estático), pausa fuera de pantalla y usa carga diferida.
- La copia web queda en `themes/museotech/assets/models/fragmented-marble-bus.glb`: 3,31 MB, frente a ~14 MB del original; texturas a 1024 px y malla simplificada al 50% (129.400 triángulos).
- Chrome con WebGL por software: modelo HTTP 200 y renderizado; ScrollTrigger progresa de 0 a 0,57 al desplazar 180 px. Verificados `1440×900` y `390×844`, sin overflow ni errores de navegador. En modo de movimiento reducido, el modelo carga estático y no se registra un trigger.
- Pasan `php -l` en plantilla y setup, `node --check` y `git diff --check`. El validador glTF no detecta errores; conserva una advertencia informativa sobre tangentes generadas en tiempo de ejecución.
- Actualización de interacción: la cabeza queda en una capa 3D detrás de la copia; el bloque de texto se fija mientras el modelo desciende por detrás con el scroll. El tamaño parte al 120% del estado anterior y aumenta progresivamente al 180% al completar el recorrido.
- El arrastre por ratón y tacto gira el modelo y deja inercia con desaceleración exponencial; al perder el impulso sigue un giro automático lento. Las flechas también lo controlan. `prefers-reduced-motion` mantiene una pose estática, sin ScrollTrigger ni giro automático, pero permite manipulación manual.
- QA Chrome Headless/WebGL: `1440×900` y `390×844`; posición del titular estable durante el tramo fijado, capa del modelo detrás del texto, carga GLB correcta, ScrollTrigger avanza y completa, arrastre ratón/táctil y movimiento tras soltar comprobados, sin overflow horizontal. Movimiento reducido: sin trigger.
- Pasan `node --check`, `php -l`, pruebas CDP de capa/posición/interacción y `git diff --check`. `make test/lint/build` sigue bloqueado porque las tareas del Makefile apuntan a `plugins/plugin-skeleton`, que no existe.
- Ajuste posterior: se eliminó el descenso y el pin ligados al scroll; el modelo queda detrás del texto en su posición, gira automáticamente al doble de velocidad y se amplió de forma adaptable (+18% aprox. tanto en escritorio como en móvil). Se conserva el drag con inercia y el respeto a movimiento reducido.
- Se compactó el espacio previo a Objetivos: Hero a 89svh en escritorio y padding superior de Objetivos de 8vh (64–96px), reducido a 48px en móvil.
- Se amplió el encuadre responsive del modelo (+17% en escritorio y +10% en móvil aprox.) y se aclaró ligeramente su mapa de color en runtime (brillo +9%, saturación -10%), preservando los mapas y propiedades originales del GLB.
- Se aclaró un poco más el material de mármol: filtro del mapa base a brillo 1.15 y saturación 0.86, manteniendo intactos los mapas de relieve y rugosidad.
- Los párrafos descriptivos de Hero, Podcast y Repositorio usan Satoshi; los títulos conservan GT America.
- Los CTA de Podcasts (“Escuchar”) y Repositorio (“Ver el repositorio”) adoptan el mismo botón tipo píldora que “Ver todos los artículos”; se conservan los destinos configurables y se retira la flecha decorativa.
