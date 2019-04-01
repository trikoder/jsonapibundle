#!/usr/bin/env bash

docker-compose build
docker-compose up -d

docker-compose run --no-deps --rm php rm -rf tests/Resources/app/cache var/cache

docker-compose run --no-deps --rm php composer install --no-interaction --no-ansi --prefer-dist
