#!/bin/bash

set -e

export DOCKER_HOST

exec ./bin/docker-compose run --rm php-cli php -f ${BINARY} -- "${ARGUMENTS}"
