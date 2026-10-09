#!/bin/sh
set -eu
: "${DB_PASSWORD:?Set DB_PASSWORD in environment}"
DEST="${1:?Usage: backup-mariadb.sh /secure/path/backup.sql.gz}"
umask 077
mkdir -p "$(dirname "$DEST")"
docker compose -f compose.yaml -f compose.mariadb.yaml exec -T mariadb \
  mariadb-dump --single-transaction --quick --user=hivepaste --password="$DB_PASSWORD" hivepaste | gzip > "$DEST"
test -s "$DEST" || { rm -f "$DEST"; echo "Backup is empty" >&2; exit 1; }
echo "Backup written to $DEST; test restore on an isolated database before relying on it."
