# Cards del blog en home, sin carrusel — 2026-10-08

- La sección de noticias de portada renderiza directamente `template-parts/content/news-card.php`, igual que `/blog/`, dentro de una cuadrícula de 3 columnas en escritorio y una en móvil.
- Retirados el markup, los estilos, la barra de progreso y el script del carrusel. La portada recupera título, descripción, cantidad y enlace configurables en el Customizer.
- `news-archive.css` se carga también en la portada para aplicar el mismo aspecto de card: imagen 4:3, categoría, fecha, título, superficie, radio, sombra y espaciado.
- Verificado en Chrome: 3 cards en home, 9 en `/blog/`; columnas 3→1, estilos calculados iguales, card móvil dentro de 390 px, sin slider ni script asociado. Contrato PHP, `php -l` y `git diff --check` pasan.
- Pendiente ajeno a esta sección: `scrollWidth` global móvil de 461 px a viewport 390 por `.home-marquee__inner`; las cards de noticias caben dentro del viewport.
