#!/bin/sh
set -eu
if [ -z "${APP_KEY:-}" ]; then
  echo 'ERROR: APP_KEY must be configured in .env (php artisan key:generate --show).' >&2
  exit 1
fi
mkdir -p /var/www/html/storage/framework/cache/data /var/www/html/storage/framework/sessions /var/www/html/storage/framework/views /var/www/html/storage/logs
if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
  touch /var/www/html/database/database.sqlite
fi
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database
php artisan migrate --force --no-interaction
exec /usr/bin/supervisord -c /etc/supervisord.conf
