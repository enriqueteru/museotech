# Skeleton WoLocal WordPress

Skeleton local para desarrollar WordPress headless con MariaDB, WordPress, WP-CLI, npm y Composer.

## Estructura

- `docker-compose.yml`: MariaDB, WordPress, WP-CLI, Node y Composer.
- `docker/wp-bootstrap.sh`: instala WordPress, configura URL, activa plugin y theme.
- `plugins/plugin-skeleton/`: plugin base con PHP, REST API y build moderno con Vite.
- `themes/theme-skeleton/`: theme minimo para desarrollo local.
- `apps/`: espacio para frontends modernos externos a WordPress.

## Plugin skeleton

Incluye una base generica para desarrollo exigente:

- Custom REST API: `/wp-json/plugin-skeleton/v1/status`, `/settings` y `/variables`.
- Menu de WordPress: `Skeleton`.
- Pagina de personalizacion con tabs: `General`, `API` y `Variables globales`.
- Subpaginas de menu: `Variables globales`, `Custom API` y `Metaboxes`.
- CPT `plugin_item` con metaboxes de ejemplo.
- Variables globales reutilizables desde admin, shortcode y API.
- Import/export JSON desde admin, REST y WP-CLI.
- WP-CLI: `status`, `migrate`, `seed`, `export`, `import`, `reset`.
- Migraciones versionadas, capability propia, cache/transients, logging y cron.
- Quality gate con PHPCS, PHPStan, PHPUnit, npm, Vite y GitHub Actions.

## Arranque rapido

```sh
cp .env.example .env
make docker-install
make docker-bootstrap
```

WordPress queda disponible en `http://localhost:8080`.

Credenciales por defecto:

- Usuario: `admin`
- Password: `admin`

## Comandos utiles

```sh
make docker-up
make docker-bootstrap
make docker-down
make docker-reset
make wp-shell
make wp ARGS="plugin list"
make wp ARGS="plugin-skeleton status"
make npm ARGS="--prefix /workspace/plugins/plugin-skeleton run build"
```

Si tienes Node y Composer instalados en local:

```sh
make install
make build
make test
make lint
make verify
```

Checks granulares:

```sh
make php-lint
make phpcs
make phpstan
make phpunit
make js-lint
make docker-verify
```

## Extras Docker

Los extras no se levantan por defecto. Usa profiles:

```sh
docker compose --profile observability up -d
docker compose --profile xdebug up -d wordpress-xdebug
```

- WordPress: `http://localhost:8080`
- WordPress con Xdebug: `http://localhost:8081`
- Adminer: `http://localhost:8082`
- MailHog: `http://localhost:8025`

## Desarrollo del plugin

El plugin se monta en:

```txt
plugins/plugin-skeleton -> /var/www/html/wp-content/plugins/plugin-skeleton
```

Incluye un shortcode inicial:

```txt
[plugin_skeleton]
```

y un endpoint REST:

```txt
/wp-json/plugin-skeleton/v1/status
/wp-json/plugin-skeleton/v1/settings
/wp-json/plugin-skeleton/v1/variables
/wp-json/plugin-skeleton/v1/export
/wp-json/plugin-skeleton/v1/import
```
