uid = $(shell id -u)
gid = $(shell id -g)

.PHONY: init build build_docker_images start stop restart enter


build_docker_images:
	@echo "Building Docker images..."
	@docker-compose build --pull --build-arg uid=$(uid) --build-arg gid=$(gid)

composer_install:
	@echo "Installing PHP dependencies..."
	#@docker-compose run --rm -u $(uid):$(gid) php composer install

build: build_docker_images composer_install

init: build

enter:
	@docker-compose run --rm -u $(uid):$(gid) php ash

restart: stop start