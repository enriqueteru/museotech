# Componente Three.js para banners de Podcast

- Corregida la ruta predeterminada del modelo del banner a `assets/models/podcast-banner.glb`, el nombre actual del archivo optimizado.
- El archivo y los estilos de Three.js se encolan en las páginas de listado con banners 3D; antes el banner de Podcasts salía sin dimensiones y no se ejecutaba el módulo del GLB.
- Los modelos 3D se limitan a los banners de listados y archivos. No se muestran en páginas individuales ni dentro de tarjetas.
- Añadido `template-parts/components/podcast-banner-model.php`, reutilizado en banners de listados. Admite cambiar la ruta del GLB, la velocidad de giro y la escala mediante argumentos del template part; usa `filemtime()` para invalidar caché.
- El componente inicial usa `Meshy_AI_Marble_Melody_1010000946_texture.glb`, mantiene giro automático configurable, controles por arrastre/flechas y movimiento estático con `prefers-reduced-motion`. La escena se carga bajo demanda, usa `GLTFLoader` y `MeshoptDecoder`, y ajusta la cámara a la geometría.
- Copia runtime optimizada en `assets/models/podcast-banner.glb`: 30,414,608 → 3,732,828 bytes; texturas 1024×1024, `EXT_meshopt_compression` y `KHR_mesh_quantization`. El original de Downloads no se modifica.
- Verificación: prueba de contrato RED→GREEN, render del componente con PHP y configuración, `php -l`, `node --check`, parseo real del GLB con `GLTFLoader`/`MeshoptDecoder`, encuadre geométrico en desktop/móvil, páginas de archivo/single y asset HTTP 200, `git diff --check` en archivos tocados. Inspección visual WebGL bloqueada: Chromium no pudo abrir `DevToolsActivePort` por permisos macOS.
- Ajuste de espaciado superior: reducido el padding superior del archivo Podcast (desktop 112–150px, móvil 96px) y del single (108px desktop, 100px tablet/móvil); reducido alto del canvas para acercar el texto sin perder el modelo.
- Segundo ajuste por exceso de espacio: archivo ahora 82–104px desktop/72px móvil; single 88px desktop/76px tablet/68px móvil, con 18px bajo el botón volver (14px móvil); canvas 210–280px desktop/160–220px móvil.
- Corregida la cascada del archivo: `.section-banner` podía volver a imponer 250–280px por tener igual especificidad; `.podcast-archive .podcast-archive__header` ahora aplica el espacio reducido con mayor especificidad.
