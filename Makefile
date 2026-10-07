DOCKER_COMPOSE ?= docker compose
PLUGIN_DIR ?= plugins/plugin-skeleton
THEME_DIR ?= themes/theme-skeleton
NPM ?= npm
COMPOSER ?= composer

.PHONY: install build dev test lint verify php-lint phpcs phpstan phpunit js-lint docker-install docker-build docker-verify docker-up docker-bootstrap docker-down docker-reset wp-shell wp npm composer

install:
	cd $(PLUGIN_DIR) && $(NPM) install
	cd $(PLUGIN_DIR) && $(COMPOSER) install

build:
	cd $(PLUGIN_DIR) && $(NPM) run build

dev:
	cd $(PLUGIN_DIR) && $(NPM) run dev

test:
	cd $(PLUGIN_DIR) && $(NPM) test
	cd $(PLUGIN_DIR) && $(COMPOSER) run test

lint:
	$(MAKE) js-lint
	$(MAKE) php-lint
	$(MAKE) phpcs

verify:
	cd $(PLUGIN_DIR) && $(COMPOSER) run verify
	cd $(PLUGIN_DIR) && $(NPM) run lint
	cd $(PLUGIN_DIR) && $(NPM) run build
	$(DOCKER_COMPOSE) config --quiet

php-lint:
	cd $(PLUGIN_DIR) && $(COMPOSER) run php-lint

phpcs:
	cd $(PLUGIN_DIR) && $(COMPOSER) run phpcs

phpstan:
	cd $(PLUGIN_DIR) && $(COMPOSER) run phpstan

phpunit:
	cd $(PLUGIN_DIR) && $(COMPOSER) run phpunit

js-lint:
	cd $(PLUGIN_DIR) && $(NPM) run lint

docker-install:
	$(DOCKER_COMPOSE) run -T --rm node npm --prefix /workspace/$(PLUGIN_DIR) ci
	$(DOCKER_COMPOSE) run -T --rm composer install

docker-build:
	$(DOCKER_COMPOSE) run -T --rm node npm --prefix /workspace/$(PLUGIN_DIR) run build

docker-verify:
	$(DOCKER_COMPOSE) config --quiet
	$(DOCKER_COMPOSE) run -T --rm composer run verify
	$(DOCKER_COMPOSE) run -T --rm node npm --prefix /workspace/$(PLUGIN_DIR) ci
	$(DOCKER_COMPOSE) run -T --rm node npm --prefix /workspace/$(PLUGIN_DIR) run lint
	$(DOCKER_COMPOSE) run -T --rm node npm --prefix /workspace/$(PLUGIN_DIR) run build

docker-up:
	$(DOCKER_COMPOSE) up -d --remove-orphans

docker-bootstrap: docker-up
	$(DOCKER_COMPOSE) run -T --rm --entrypoint=sh wpcli /docker/wp-bootstrap.sh

docker-down:
	$(DOCKER_COMPOSE) down

docker-reset:
	$(DOCKER_COMPOSE) down -v

wp-shell:
	$(DOCKER_COMPOSE) run --rm --entrypoint=sh wpcli

wp:
	$(DOCKER_COMPOSE) run -T --rm wpcli $(ARGS) --allow-root

npm:
	$(DOCKER_COMPOSE) run -T --rm node npm $(ARGS)

composer:
	$(DOCKER_COMPOSE) run -T --rm composer $(ARGS)
