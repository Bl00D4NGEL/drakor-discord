DOCKER_COMPOSE_CMD=docker-compose -f docker-compose.yml

help:                                                                           ## shows this help
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_\-\.]+:.*?## / {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

setup: 																			## setup env
	@cp .env.skeleton .env
	build

build:																			## build containers
	$(DOCKER_COMPOSE_CMD) build

start:																			## start containers
	$(DOCKER_COMPOSE_CMD) up -d

stop:																			## stop containers
	$(DOCKER_COMPOSE_CMD) down

console:																		## run cli php container
	$(DOCKER_COMPOSE_CMD) exec php bash

.PHONY: help setup build start stop console
