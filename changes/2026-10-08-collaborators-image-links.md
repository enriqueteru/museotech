# Colaboradores con imagen enlazada a su web — 2026-10-08

- Las tarjetas del archivo muestran únicamente la imagen destacada; desaparecen nombre, enlace al perfil interno y CTA.
- Cada imagen enlaza al campo real `web` del colaborador. La tarjeta solo se renderiza si existen imagen y URL, sin inventar destinos.
- Presentación centrada con `object-fit: contain`, cursor pointer, hover sutil sin alterar el flujo y foco visible.
- Verificado en Chrome a 1440×900 y 390×844: 2 de 2 imágenes cargadas, URLs externas correctas, contenido de tarjeta vacío aparte de la imagen, sin overflow; PHP lint y `git diff --check` pasan.
