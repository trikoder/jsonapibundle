#!/usr/bin/env bash

docker-compose run --no-deps --rm -p 8000:8000 php php tests/Resources/app/bin/console server:run -d ./tests/Resources/app/web 0.0.0.0