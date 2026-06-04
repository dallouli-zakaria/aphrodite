#!/bin/sh
set -e

PORT="${PORT:-8000}"

sed -ri "s/^Listen .*/Listen ${PORT}/" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \*:[0-9]+>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration --env=prod
php bin/console app:seed-catalog --env=prod

exec "$@"
