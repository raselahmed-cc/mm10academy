#!/usr/bin/env bash
set -euo pipefail

# Usage:
#   ./ops/restore_live.sh /var/www/mm10academy /var/backups/mm10academy/20260503-120000
#
# Expects in backup dir:
#   - files-*.tar.gz
#   - db-*.sql.gz

SITE_PATH="${1:?Site path is required}"
BACKUP_DIR="${2:?Backup directory is required}"

FILES_ARCHIVE="$(ls -1 "${BACKUP_DIR}"/files-*.tar.gz | head -1)"
DB_ARCHIVE="$(ls -1 "${BACKUP_DIR}"/db-*.sql.gz | head -1)"

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

ROLLBACK_TS="$(date +%Y%m%d-%H%M%S)"
ROLLBACK_DIR="${SITE_PATH}/../rollback-${ROLLBACK_TS}"
mkdir -p "${ROLLBACK_DIR}"

echo "Creating emergency rollback backup before restore..."
tar -czf "${ROLLBACK_DIR}/pre-restore-files-${ROLLBACK_TS}.tar.gz" .
mysqldump -h "${DB_HOST}" -P "${DB_PORT}" -u "${DB_USER}" "--password=${DB_PASS}" --single-transaction "${DB_NAME}" | gzip -c > "${ROLLBACK_DIR}/pre-restore-db-${ROLLBACK_TS}.sql.gz"

cleanup() {
  rm -f "${SITE_PATH}/.maintenance"
}
trap cleanup EXIT

# Put site in maintenance mode.
printf "<?php \$upgrading = %s; ?>" "$(date +%s)" > "${SITE_PATH}/.maintenance"

echo "Restoring files from ${FILES_ARCHIVE}..."
# Keep current wp-config.php and .env untouched.
tar -xzf "${FILES_ARCHIVE}" --exclude='wp-config.php' --exclude='.env' -C "${SITE_PATH}"

echo "Restoring database from ${DB_ARCHIVE}..."
mysql -h "${DB_HOST}" -P "${DB_PORT}" -u "${DB_USER}" "--password=${DB_PASS}" -e "SET FOREIGN_KEY_CHECKS=0;" "${DB_NAME}"
zcat "${DB_ARCHIVE}" | mysql -h "${DB_HOST}" -P "${DB_PORT}" -u "${DB_USER}" "--password=${DB_PASS}" "${DB_NAME}"
mysql -h "${DB_HOST}" -P "${DB_PORT}" -u "${DB_USER}" "--password=${DB_PASS}" -e "SET FOREIGN_KEY_CHECKS=1;" "${DB_NAME}"

# Fix permissions (adjust user/group if needed)
find "${SITE_PATH}" -type d -exec chmod 755 {} \;
find "${SITE_PATH}" -type f -exec chmod 644 {} \;
[ -f "${SITE_PATH}/wp-config.php" ] && chmod 600 "${SITE_PATH}/wp-config.php" || true
[ -f "${SITE_PATH}/.env" ] && chmod 600 "${SITE_PATH}/.env" || true

echo "Restore completed successfully."
echo "Rollback backup is available at: ${ROLLBACK_DIR}"
