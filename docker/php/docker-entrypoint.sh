#!/bin/sh
# TODO: clear laravel cache here if possible, or else it may crash composer

composer install

# if .env does not exist, we assume this is the first execution
if [ ! -f .env ] && [ "${APP_ENV}" = "docker-dev" ]
then
  echo ".env not found, assuming first execution: initialising .env and seeding database..."
  cp .env.example .env
  php artisan key:generate
  php artisan config:cache
  echo "Waiting 10 seconds just in case DB was not fully started"
  sleep 10
  php artisan migrate:refresh --seed
  echo "...done."
else
  php artisan config:cache
fi

/usr/local/bin/docker-php-entrypoint "$@"
