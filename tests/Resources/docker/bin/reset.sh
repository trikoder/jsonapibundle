#!/usr/bin/env bash

# docker-compose down

rm -rf ./../../app/cache/*
rm -rf ./../../app/logs/*

./bin/build.sh

# TODO - add reset fixtures