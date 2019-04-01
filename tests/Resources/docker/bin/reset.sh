#!/usr/bin/env bash


# clean up on old and new way
docker-compose run --no-deps --rm php rm -rf tests/Resources/app/cache/* var/cache/*
docker-compose run --no-deps --rm php rm -rf tests/Resources/app/logs/* var/logs/*

# docker-compose down

./bin/build.sh
./bin/setup_fixtures.sh
# TODO - add reset fixtures
