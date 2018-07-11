#!/usr/bin/env bash

./bin/reset.sh

docker-compose run --no-deps --rm php rm -rf tests/Resources/app/cache/test/*

docker-compose run --no-deps --rm php vendor/bin/phpunit --debug --coverage-text=php://stdout --coverage-html=logs/coverage

docker-compose run --no-deps --rm php php vendor/bin/security-checker security:check composer.lock
