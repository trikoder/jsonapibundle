#!/usr/bin/env bash

./bin/reset.sh

docker-compose run --no-deps --rm php vendor/bin/phpunit --colors=never --debug --coverage-text=php://stdout --coverage-html=logs/coverage