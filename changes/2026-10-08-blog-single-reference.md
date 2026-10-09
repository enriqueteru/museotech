# Single editorial del blog según referencia

- Reorganiza el single de `post` en cabecera, portada, cuerpo y sidebar con autor, fechas e índice generado desde los encabezados reales.
- La portada usa la imagen destacada; si también aparece como bloque Gutenberg en el contenido, se retira esa repetición sin alterar las demás imágenes.
- Cuando el artículo no tiene encabezados, el índice ofrece un enlace a «Introducción». La llamada a participar enlaza a Contacto y las tarjetas finales siguen siendo dinámicas.
- Maquetación responsive revisada en 1440×900 y 390×844. En escritorio, el sidebar permanece sticky; en móvil vuelve al flujo. El ancla respeta el header fijo y no hay overflow horizontal.
- Datos locales: los 47 artículos publicados tienen portada y no contienen H2/H3; por ello se verificó el fallback del índice. El texto secundario cumple contraste AA en blanco (5,17:1) y beige (4,64:1).
