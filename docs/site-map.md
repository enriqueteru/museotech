# Museotech: inventario y sitemap

## Estado del contenido restaurado

-  página `Home` (`/`, ID 140).
- Página de entradas: `Blog` (`/blog/`, ID 177), con 47 entradas publicadas.
- Páginas publicadas: Home, Blog, Contacto, Aviso legal, Descargo de responsabilidad, Política de cookies y Políticas de privacidad.
- CPT `podcast`: 10 publicados. Campos ACF: institución, descripción, audio, fecha de grabación y entrevistado.
- CPT `repositorios`: 34 publicados. Campos ACF: archivo, recurso externo, autores, editorial/revista, URI, ISBN/ISSN, fecha y páginas.
- CPT `equipo`: 7 publicados. Campo ACF: bio. Taxonomía: `rol`.
- CPT `colaborador`: 2 publicados. Campo ACF: web.
- Taxonomías editoriales activas: `formato` (repositorios) y `rol` (equipo). `experiencia` tiene 2 términos huérfanos en la BD, pero no está registrada ni asociada actualmente; no se migra hasta decidir su uso.
- Las entradas usan categorías y etiquetas.

## Sitemap propuesto

```text
/
├── Blog                         /blog/
│   ├── Categorías               /category/{slug}/
│   └── Etiquetas                /tag/{slug}/
├── Podcasts                     /podcasts/       archivo CPT
│   └── Episodio                  /podcast/{slug}/
├── Equipo                       /equipo/         archivo CPT
│   └── Perfil                    /equipo/{slug}/
├── Repositorio                  /repositorios/   archivo CPT
│   └── Recurso                  /repositorios/{slug}/
├── Colaboradores                /colaboradores/  archivo CPT
│   └── Perfil                   /colaborador/{slug}/
├── Contacto                     /contacto/
└── Legal (pie de página)
    ├── Aviso legal              /aviso-legal/
    ├── Políticas de privacidad  /politicas-de-privacidad/
    ├── Política de cookies      /politica-de-cookies/
    └── Descargo de responsabilidad /descargo-de-responsabilidad/
```

El menú Principal actual enlaza Equipo, Podcast, Repositorio, Colaboradores, Blog y Contacto. El menú Legal no incluye Descargo de responsabilidad; queda señalado para decidir si se añade. Las rutas de archivo y de entrada individual se mantienen con Museotech Core.

## Dependencias que requieren migración

- Elementor dejó `_elementor_data` en 6 páginas, 36 entradas y 29 plantillas de biblioteca. Las seis páginas se migraron o asignaron a partes nativas. Las 36 entradas ya tenían cuerpos HTML nativos y sus valores `_elementor_data` estaban vacíos; no se reescribieron. Los JSON originales se conservan en la BD, pero no se usan para renderizar el sitio.
- ACF registra actualmente los cuatro CPT y sus campos. Los valores están en metadatos y se conservan en la BD, pero desactivar ACF antes de trasladar el registro ocultaría los CPT y sus controles de edición.
- `Elementor`, `Elementor Pro` y `Music Player for Elementor` están desactivados. Sus directorios y metadatos se conservan para rollback. Museotech usa el campo de audio existente y un reproductor HTML nativo.
- ACF se mantiene activo para editar los campos existentes. Real Media Library Lite y Simple Custom Post Order quedan pendientes de comprobar en el admin antes de decidir si se eliminan.
- Duplicator se conserva hasta terminar el restore y la limpieza de instalador/archivo.

## Extracción inicial a Museotech

- La portada se descompone en cinco template parts: presentación, objetivos, podcasts, repositorio y noticias.
- El texto, los enlaces y la imagen original de portada pasan a valores iniciales editables en `Apariencia > Personalizar > Museotech`. Las descripciones se editan como texto con párrafos, no como HTML de Elementor.
- Los seis objetivos conservan su texto y usan selectores semánticos de icono, sin clases Font Awesome.
- Las noticias de portada consultan las entradas publicadas y permiten cambiar su cantidad desde el Customizer. Podcasts, repositorios y perfiles continúan siendo contenido CPT, no copias en opciones de tema.
- Las introducciones y cantidades de cada archivo CPT son personalizables. El footer expone crédito, logotipos, enlaces sociales y texto legal.
- Dos transiciones mosaico conectan objetivos con podcasts y repositorio con noticias. Sus imágenes se cambian desde `Apariencia > Personalizar > Museotech > Home: transiciones entre secciones`, con fotos del Museo Mercedes-Benz como defaults.
- El panel incluye 60 controles agrupados por sección. Los valores extraídos son defaults del tema; al personalizarlos se guardan como opciones propias de Museotech.
- Contacto conserva su cuerpo nativo y usa `[museotech_contact_form]`. El formulario incluye nombre, empresa, email, presupuesto, mensaje, aceptación de privacidad, honeypot y envío por `wp_mail`.
- El contenido legal se copió desde el campo ACF `contenido` a `post_content`; el valor anterior se respaldó en `_museotech_pre_migration_content`.
- Contacto usa `templates/page-contact.php`; las cuatro páginas legales usan `templates/page-legal.php`. Home y Blog usan `front-page.php` y `home.php` por jerarquía. Header, footer, archives, singles y loop cards tienen equivalentes nativos.
- Cada página migrada conserva `_elementor_data`; el valor anterior de `_elementor_edit_mode` se guarda en `_museotech_pre_migration_elementor_edit_mode` y el marcador activo se elimina para evitar que Elementor intercepte `the_content`.
- El dump previo está en `backups/museotech-pre-elementor-migration.sql`. `docker/migrate-elementor-content.php` se ejecuta con WP-CLI, es idempotente y admite `MUSEOTECH_MIGRATION_DRY_RUN=1`.

## Estructura del tema

```text
themes/museotech/
├── inc/                         setup, CPT/taxonomías y helpers
├── template-parts/
│   ├── archive/                 listados
│   ├── content/                 tarjetas por tipo de contenido
│   ├── sections/                datos específicos de cada CPT
│   └── single/                  composición de fichas
├── archive-{podcast,repositorios,equipo,colaborador}.php
├── single-{podcast,repositorios,equipo,colaborador}.php
├── front-page.php
├── home.php
├── page.php
└── single.php / archive.php / index.php
```

El tema Museotech y el plugin Museotech Core están activos. ACF permanece activo porque los campos personalizados existentes todavía se editan con ese plugin.