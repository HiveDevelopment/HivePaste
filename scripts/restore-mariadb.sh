#!/bin/sh
set -eu
: "${DB_PASSWORD:?Set DB_PASSWORD in environment}"
FILE="${1:?Usage: restore-mariadb.sh backup.sql.gz --confirm-destructive}"
[ "${2:-}" = "--confirm-destructive" ] || { echo "Restore overwrites database content. Add --confirm-destructive" >&2; exit 1; }
[ -s "$FILE" ] || { echo "Backup missing or empty" >&2; exit 1; }
gzip -t "$FILE"
gzip -dc "$FILE" | docker compose -f compose.yaml -f compose.mariadb.yaml exec -T mariadb \
  mariadb --user=hivepaste --password="$DB_PASSWORD" hivepaste
echo "Restore finished. Verify paste counts and application behaviour."
