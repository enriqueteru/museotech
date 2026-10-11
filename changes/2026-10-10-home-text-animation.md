# Transiciones de texto para la portada

- El titular del hero se divide según sus líneas renderizadas y se revela en secuencia con máscaras; su texto de apoyo entra desde 16 px abajo.
- El hero y los efectos magnéticos esperan al evento emitido después de ocultar el loader; los textos y contadores usan ScrollTrigger, con `top 92%` por defecto y `top 78%` para objetivos.
- Los textos animados permanecen ocultos mientras el loader sale, para que GSAP los revele después de la transición.
- Las líneas enmascaradas conservan la alineación del heading, incluido el centrado del titular del hero.
- Los títulos y textos de las secciones de objetivos, podcast, repositorio y noticias se revelan al entrar en pantalla.
- Los banners de los listados de Blog, Podcasts, Repositorio y Colaboradores revelan primero el título y después sus textos, al terminar el loader.
- Las entradas, encabezados de resultados y paginación de esos listados aparecen después, escalonados al entrar en pantalla; las fichas individuales quedan fuera.
- La numeración 01–06 cuenta desde cero al entrar en viewport; los botones destacados incorporan respuesta magnética limitada a 18 px en horizontal y 11 px en vertical.
- Se respeta `prefers-reduced-motion` y se ajusta el ritmo del hero en escritorio de poca altura.
