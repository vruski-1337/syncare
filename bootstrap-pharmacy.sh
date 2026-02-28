#!/usr/bin/env bash
set -euo pipefail

APP_DIR="/workspaces/syncare/pharmacy"
APP_NAME="syncare_pharmacy"
DB_MODE="sqlite"
PHP_VERSION="8.3"
SERVER_PORT="8000"

usage() {
  cat <<EOF
Usage: $0 [--app-dir PATH] [--db sqlite|mysql] [--port 8000]

Options:
  --app-dir PATH   Laravel app path (default: /workspaces/syncare/pharmacy)
  --db MODE        Database mode: sqlite (default) or mysql
  --port PORT      Laravel serve port (default: 8000)
EOF
}

while [[ $# -gt 0 ]]; do
  case "$1" in
    --app-dir)
      APP_DIR="$2"
      shift 2
      ;;
    --db)
      DB_MODE="$2"
      shift 2
      ;;
    --port)
      SERVER_PORT="$2"
      shift 2
      ;;
    -h|--help)
      usage
      exit 0
      ;;
    *)
      echo "Unknown option: $1"
      usage
      exit 1
      ;;
  esac
done

if [[ ! -d "$APP_DIR" ]]; then
  echo "App directory not found: $APP_DIR"
  exit 1
fi

if [[ "$EUID" -eq 0 ]]; then
  SUDO=""
else
  SUDO="sudo"
fi

echo "[1/8] Installing base packages..."
$SUDO apt update
$SUDO apt install -y \
  ca-certificates \
  curl \
  unzip \
  git \
  gnupg \
  lsb-release \
  software-properties-common

echo "[2/8] Installing PHP, extensions, and web server components..."
$SUDO apt install -y \
  php${PHP_VERSION} \
  php${PHP_VERSION}-cli \
  php${PHP_VERSION}-fpm \
  php${PHP_VERSION}-common \
  php${PHP_VERSION}-mbstring \
  php${PHP_VERSION}-xml \
  php${PHP_VERSION}-curl \
  php${PHP_VERSION}-zip \
  php${PHP_VERSION}-bcmath \
  php${PHP_VERSION}-intl \
  php${PHP_VERSION}-sqlite3 \
  php${PHP_VERSION}-mysql \
  nginx

echo "[3/8] Installing Composer (if missing)..."
if ! command -v composer >/dev/null 2>&1; then
  EXPECTED_CHECKSUM="$(php -r 'copy("https://composer.github.io/installer.sig", "php://stdout");')"
  php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
  ACTUAL_CHECKSUM="$(php -r "echo hash_file('sha384', 'composer-setup.php');")"
  if [[ "$EXPECTED_CHECKSUM" != "$ACTUAL_CHECKSUM" ]]; then
    >&2 echo 'Invalid Composer installer checksum'
    rm -f composer-setup.php
    exit 1
  fi
  php composer-setup.php --quiet
  rm -f composer-setup.php
  $SUDO mv composer.phar /usr/local/bin/composer
fi

echo "[4/8] Installing Node.js LTS (if missing)..."
if ! command -v node >/dev/null 2>&1; then
  curl -fsSL https://deb.nodesource.com/setup_lts.x | $SUDO -E bash -
  $SUDO apt install -y nodejs
fi

echo "[5/8] Installing MySQL server (optional mode: mysql)..."
if [[ "$DB_MODE" == "mysql" ]]; then
  $SUDO apt install -y mysql-server
  $SUDO systemctl enable mysql || true
  $SUDO systemctl start mysql || true
fi

echo "[6/8] Installing Laravel app dependencies..."
cd "$APP_DIR"
composer install --no-interaction --prefer-dist
npm install
npm run build

if [[ ! -f .env ]]; then
  cp .env.example .env
fi

if [[ "$DB_MODE" == "sqlite" ]]; then
  touch database/database.sqlite
  sed -i 's/^DB_CONNECTION=.*/DB_CONNECTION=sqlite/' .env
  sed -i 's/^DB_DATABASE=.*/DB_DATABASE=database\/database.sqlite/' .env
  sed -i 's/^DB_HOST=.*/DB_HOST=127.0.0.1/' .env
  sed -i 's/^DB_PORT=.*/DB_PORT=3306/' .env
  sed -i 's/^DB_USERNAME=.*/DB_USERNAME=root/' .env
  sed -i 's/^DB_PASSWORD=.*/DB_PASSWORD=/' .env
else
  MYSQL_DB="${APP_NAME}"
  MYSQL_USER="${APP_NAME}_user"
  MYSQL_PASS="change_me_please"

  $SUDO mysql -e "CREATE DATABASE IF NOT EXISTS ${MYSQL_DB};"
  $SUDO mysql -e "CREATE USER IF NOT EXISTS '${MYSQL_USER}'@'localhost' IDENTIFIED BY '${MYSQL_PASS}';"
  $SUDO mysql -e "GRANT ALL PRIVILEGES ON ${MYSQL_DB}.* TO '${MYSQL_USER}'@'localhost'; FLUSH PRIVILEGES;"

  sed -i 's/^DB_CONNECTION=.*/DB_CONNECTION=mysql/' .env
  sed -i 's/^DB_HOST=.*/DB_HOST=127.0.0.1/' .env
  sed -i 's/^DB_PORT=.*/DB_PORT=3306/' .env
  sed -i "s/^DB_DATABASE=.*/DB_DATABASE=${MYSQL_DB}/" .env
  sed -i "s/^DB_USERNAME=.*/DB_USERNAME=${MYSQL_USER}/" .env
  sed -i "s/^DB_PASSWORD=.*/DB_PASSWORD=${MYSQL_PASS}/" .env
fi

echo "[7/8] Preparing Laravel application..."
php artisan key:generate --force
php artisan migrate --seed --force

if ! grep -q "^APP_URL=" .env; then
  echo "APP_URL=http://localhost:${SERVER_PORT}" >> .env
else
  sed -i "s|^APP_URL=.*|APP_URL=http://localhost:${SERVER_PORT}|" .env
fi

echo "[8/8] Starting Laravel development server..."
echo "Server URL: http://localhost:${SERVER_PORT}"
php artisan serve --host=0.0.0.0 --port="${SERVER_PORT}"
