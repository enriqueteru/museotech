# Modelo 3D en el podcast

Se integra el GLB optimizado `echoes-of-marble.glb` en el hero de cada podcast individual, detrás del contenido, con rotación, controles de arrastre y flechas, y compatibilidad con movimiento reducido. La sección Podcast de la portada se conserva sin el modelo.

El modelo se carga bajo demanda al entrar en pantalla; el import map de Three.js y MeshoptDecoder se habilitan para los singles de podcast. `/podcast` redirige actualmente al single publicado (`/podcast/podcast-1/`) y el modelo se presenta allí.
