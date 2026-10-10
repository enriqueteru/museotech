# Modelo Three.js para banners del Blog

- Reutilizado el componente configurable del banner Podcast para mostrar `pluma.glb` sobre los textos del archivo Blog y las entradas individuales; permite configurar ruta GLB, escala, giro y clase visual por instancia.
- Copia de ejecución optimizada en `assets/models/blog-pluma.glb`: 15,659,208 → 991,060 bytes, con texturas limitadas a 1024px y compresión Meshopt. El original de Downloads no se modifica.
- Encolados Three.js, import map y estilos sólo en archivo Blog y entradas; giro automático, arrastre, flechas y `prefers-reduced-motion` siguen el comportamiento existente.
- Espaciado de archivo y entrada individual alineado con los ajustes actuales de Podcast.
