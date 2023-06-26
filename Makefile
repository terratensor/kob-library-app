init: docker-down \
	app-clear \
	docker-pull docker-build docker-up
up: docker-up
down: docker-down
restart: down up

docker-down:
	docker compose down --remove-orphans

app-clear:
	docker run --rm -v ${PWD}/app:/app -w /app alpine sh -c 'rm -rf app/var/cache/* var/log/* var/test/*'
	docker run --rm -v ${PWD}/app:/app -w /app alpine sh -c 'rm -rf app/runtime/cache/*'

docker-pull:
	docker compose pull

docker-build:
	DOCKER_BUILDKIT=1 COMPOSE_DOCKER_CLI_BUILD=1 docker-compose build --build-arg BUILDKIT_INLINE_CACHE=1 --pull

docker-up:
	docker compose up -d
