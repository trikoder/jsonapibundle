#!/usr/bin/env bash

docker-compose build
docker-compose up -d
docker-compose run --no-deps --rm php composer install --dev --no-interaction --no-ansi --prefer-dist

docker-compose run --no-deps --rm php php tests/Resources/app/bin/console doctrine:database:create --if-not-exists
docker-compose run --no-deps --rm php php tests/Resources/app/bin/console doctrine:schema:drop --force
docker-compose run --no-deps --rm php php tests/Resources/app/bin/console doctrine:schema:update --no-interaction --force
docker-compose run --no-deps --rm php php tests/Resources/app/bin/console doctrine:fixtures:load --no-interaction --fixtures=./tests/Resources/DataFixtures/ORM/

