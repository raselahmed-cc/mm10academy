#!/usr/bin/env bash
set -euo pipefail

# Usage:
#   ./ops/backup_live.sh /var/www/mm10academy /var/backups/mm10academy
#
# Creates:
#   - files-YYYYmmdd-HHMMSS.tar.gz
#   - db-YYYYmmdd-HHMMSS.sql.gz
#   - manifest-YYYYmmdd-HHMMSS.txt

SITE_PATH="${1:?Site path is required}"
BACKUP_ROOT="${2:?Backup path is required}"
TS="$(date +%Y%m%d-%H%M%S)"
RUN_DIR="${BACKUP_ROOT}/${TS}"

mkdir -p "${RUN_DIR}"

cd "${SITE_PATH}"

if [[ ! -f "wp-config.php" ]]; then
  echo "wp-config.php not found in ${SITE_PATH}" >&2
  exit 1
fi

DB_NAME="$(grep -E "define\(\s*'DB_NAME'" wp-config.php | head -1 | sed -E "s/.*'DB_NAME'\s*,\s*'([^']+)'.*/\1/")"
DB_USER="$(grep -E "define\(\s*'DB_USER'" wp-config.php | head -1 | sed -E "s/.*'DB_USER'\s*,\s*'([^']+)'.*/\1/")"
DB_PASS="$(grep -E "define\(\s*'DB_PASSWORD'" wp-config.php | head -1 | sed -E "s/.*'DB_PASSWORD'\s*,\s*'([^']+)'.*/\1/")"
DB_HOST_RAW="$(grep -E "define\(\s*'DB_HOST'" wp-config.php | head -1 | sed -E "s/.*'DB_HOST'\s*,\s*'([^']+)'.*/\1/")"
DB_HOST="${DB_HOST_RAW%%:*}"
DB_PORT="${DB_HOST_RAW##*:}"
if [[ "${DB_HOST}" == "${DB_PORT}" ]]; then
  DB_PORT="3306"
fi

FILES_ARCHIVE="${RUN_DIR}/files-${TS}.tar.gz"
DB_ARCHIVE="${RUN_DIR}/db-${TS}.sql.gz"
MANIFEST="${RUN_DIR}/manifest-${TS}.txt"

echo "Creating file backup..."
tar \
  --exclude='./wp-content/cache' \
  --exclude='./wp-content/litespeed' \
  --exclude='./wp-content/upgrade' \
  --exclude='./wp-content/upgrade-temp-backup' \
  -czf "${FILES_ARCHIVE}" .

echo "Creating database backup..."
mysqldump \
  -h "${DB_HOST}" \
  -P "${DB_PORT}" \
  -u "${DB_USER}" \
  "--password=${DB_PASS}" \
  --single-transaction \
  --routines \
  --triggers \
  --events \
  "${DB_NAME}" | gzip -c > "${DB_ARCHIVE}"

echo "Backup timestamp: ${TS}" > "${MANIFEST}"
echo "Site path: ${SITE_PATH}" >> "${MANIFEST}"
echo "DB name: ${DB_NAME}" >> "${MANIFEST}"
echo "Files archive: ${FILES_ARCHIVE}" >> "${MANIFEST}"
echo "DB archive: ${DB_ARCHIVE}" >> "${MANIFEST}"

sha256sum "${FILES_ARCHIVE}" "${DB_ARCHIVE}" >> "${MANIFEST}"

echo "Backup completed: ${RUN_DIR}"
