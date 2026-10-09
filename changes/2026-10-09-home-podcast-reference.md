# Home: sección Podcast con composición editorial y modelo 3D

- Se sustituyó la composición centrada de Podcast por un layout de dos columnas, inspirado en la referencia visual: texto a la izquierda y panel visual a la derecha.
- El panel visual integra el GLB optimizado `echoes-of-marble.glb`, con controles de interacción existentes, cache busting por `filemtime` y renderizado condicionado a que el archivo exista.
- En responsive (hasta 760 px), se invierte el apilado: primero aparece el modelo y después el texto. Escritorio conserva texto a la izquierda y modelo a la derecha; se mantienen textos y destino configurables desde WordPress.
- Verificación: lint PHP, `node --check` y `git diff --check` correctos; el GLB existe (803476 bytes). La revisión visual en escritorio y móvil queda bloqueada por permisos pendientes de Screen Recording/Accessibility en macOS.
