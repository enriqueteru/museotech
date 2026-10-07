#!/bin/sh
set -eu

WP_PATH="${WP_PATH:-/var/www/html}"
WP_URL="${WP_URL:-http://localhost:8080}"
WP_TITLE="${WP_TITLE:-WordPress local}"
WP_ADMIN_USER="${WP_ADMIN_USER:-admin}"
WP_ADMIN_PASSWORD="${WP_ADMIN_PASSWORD:-admin}"
WP_ADMIN_EMAIL="${WP_ADMIN_EMAIL:-admin@example.com}"

cd "$WP_PATH"

echo "Waiting for WordPress files..."
attempts=0
while [ ! -f wp-load.php ]; do
  attempts=$((attempts + 1))
  if [ "$attempts" -ge 60 ]; then
    echo "WordPress files were not found in $WP_PATH"
    exit 1
  fi
  sleep 2
done

echo "Preparing uploads directory..."
mkdir -p wp-content/uploads
chown -R 33:33 wp-content/uploads 2>/dev/null || true
chmod -R u+rwX,g+rwX wp-content/uploads 2>/dev/null || true

echo "Waiting for database..."
attempts=0
while ! wp db check --allow-root >/dev/null 2>&1; do
  attempts=$((attempts + 1))
  if [ "$attempts" -ge 60 ]; then
    echo "Database is not available"
    exit 1
  fi
  sleep 2
done

if ! wp core is-installed --allow-root >/dev/null 2>&1; then
  wp core install \
    --url="$WP_URL" \
    --title="$WP_TITLE" \
    --admin_user="$WP_ADMIN_USER" \
    --admin_password="$WP_ADMIN_PASSWORD" \
    --admin_email="$WP_ADMIN_EMAIL" \
    --skip-email \
    --allow-root
else
  echo "WordPress is already installed"
fi

wp option update home "$WP_URL" --allow-root
wp option update siteurl "$WP_URL" --allow-root

if wp plugin is-installed plugin-skeleton --allow-root >/dev/null 2>&1; then
  wp plugin activate plugin-skeleton --allow-root || true
fi

if wp theme is-installed theme-skeleton --allow-root >/dev/null 2>&1; then
  wp theme activate theme-skeleton --allow-root || true
fi

wp rewrite structure '/%postname%/' --allow-root
wp rewrite flush --allow-root

echo "WordPress ready at $WP_URL"
