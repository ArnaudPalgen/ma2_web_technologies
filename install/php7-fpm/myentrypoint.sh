#!/bin/sh
echo "Trying my-entrypoint"

CONTAINER_ALREADY_STARTED=START

if [ -f "$CONTAINER_ALREADY_STARTED" ]; then
    echo "-- Not first container startup --"
else
    echo "-- First container startup --"
    touch "$CONTAINER_ALREADY_STARTED"
    php bin/console doctrine:migrations:migrate
    php bin/console app:init-db
fi

echo "entrypoint done"