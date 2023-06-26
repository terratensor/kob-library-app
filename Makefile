init: docker-down \
	app-clear \
	docker-pull docker-build docker-up
up: docker-up
down: docker-down
restart: down up

app-clear:
	docker run --rm -v ${PWD}/app:/app -w /app alpine sh -c 'rm -rf app/var/cache/* var/log/* var/test/*'
	docker run --rm -v ${PWD}/app:/app -w /app alpine sh -c 'rm -rf app/runtime/cache/*'

docker-pull:
	docker compose pull

docker-build:
	DOCKER_BUILDKIT=1 COMPOSE_DOCKER_CLI_BUILD=1 docker-compose build --build-arg BUILDKIT_INLINE_CACHE=1 --pull

push-dev-cache:
	docker-compose push

docker-up:
	docker compose up -d

docker-down:
	docker compose down --remove-orphans

docker-down-clear:
	docker-compose down -v --remove-orphans

build:
	DOCKER_BUILDKIT=1 docker --log-level=debug build --pull --build-arg BUILDKIT_INLINE_CACHE=1 \
        --cache-from ${REGISTRY}/kob-library-app:cache \
        --tag ${REGISTRY}/kob-library-app:cache \
        --tag ${REGISTRY}/kob-library-app:${IMAGE_TAG} \
        --file app/docker/production/nginx/Dockerfile app
