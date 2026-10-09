# Espaciado y estados del formulario de contacto — 2026-10-09

- Corregido el desbordamiento de inputs con `box-sizing: border-box`, que hacía que las columnas se solaparan.
- Aumentado el espacio horizontal a 24px; se conserva la separación vertical de 25px y el diseño de una columna en móvil.
- Hover usa un subrayado gris sutil, distinto del foco negro con contorno visible para teclado; no mueve ni escala los campos.
- Chrome comprobó 1440px y 390px: columnas sin solape, estados hover/focus diferenciados y sin overflow horizontal. `git diff --check` pasa.
