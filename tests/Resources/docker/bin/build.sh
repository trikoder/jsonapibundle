#!/usr/bin/env bash

docker-compose build
docker-compose up -d

docker-compose run --no-deps --rm php rm -r tests/Resources/app/cache

docker-compose run --no-deps --rm php composer install --no-interaction --no-ansi --prefer-dist
