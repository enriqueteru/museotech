# Modelo Three.js para banners de Repositorio

- Reutilizado el componente configurable de banners para mostrar `libro.glb` en el archivo de Repositorio y en cada ficha individual; giro automático, arrastre, flechas y `prefers-reduced-motion`.
- Copia optimizada en `assets/models/repository-book.glb`: 19,691,724 → 1,863,008 bytes, texturas a 1024px, simplificación geométrica y compresión Meshopt; el archivo original de Downloads permanece intacto.
- Cargados los estilos, Three.js y el import map en archivo/perfil de Repositorio; espaciado del banner alineado con Blog, Podcast y Colaboradores.
- Verificación: inspección real del GLB optimizado, PHP lint, `node --check`, `git diff --check`; archivo `/repositorios/`, modelo y HTML renderizado responden correctamente (HTTP 200; markup, script, import map y CSS presentes).
- Añadido un enlace accesible y táctil para volver desde cada ficha individual al archivo de repositorios.
