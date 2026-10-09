# Menú fijo sin scroll — 2026-10-08

- El overlay queda limitado al viewport y el contenido usa `border-box`, eliminando el scroll causado por sumar padding a `min-height: 100%`.
- Se compactan filas, logo y redes solo en ventanas de poca altura para mantener visibles los seis enlaces.
- Retirada la etiqueta «[explorar]» del walker y sus estilos asociados.
- Verificado en Chrome a 1440×900, 390×844, 390×667 y 844×390: sin scroll interno ni contenido fuera del viewport; ningún nodo «explorar».
- `php -l`, `node --check` y `git diff --check` pasan.
