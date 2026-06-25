#!/usr/bin/env bash
# FormCraft — aqualime.uz/forms deploy skripti
# PHP versiya fayllariga (.user.ini, php.ini) TEGMAYDI.
#
# Ishlatish:
#   export AHOST_FTP_HOST="ftp.aqualime.uz"
#   export AHOST_FTP_USER="cpanel_username"
#   export AHOST_FTP_PASS="your_password"
#   ./deploy/aqualime/deploy.sh
#
# yoki bir qator:
#   AHOST_FTP_USER=user AHOST_FTP_PASS=pass ./deploy/aqualime/deploy.sh

set -euo pipefail

ROOT="$(cd "$(dirname "$0")/../.." && pwd)"
cd "$ROOT"

HOST="${AHOST_FTP_HOST:-ftp.aqualime.uz}"
USER="${AHOST_FTP_USER:-}"
PASS="${AHOST_FTP_PASS:-}"

if [[ -z "$USER" || -z "$PASS" ]]; then
  echo "Xato: AHOST_FTP_USER va AHOST_FTP_PASS kerak."
  echo "cPanel → FTP Accounts dan username oling (email emas, ko'pincha aqualime yoki shunga o'xshash)."
  exit 1
fi

echo "==> 1/5 Build (npm + composer production)..."
npm ci
npm run build
composer install --no-dev --optimize-autoloader --no-interaction

echo "==> 2/5 Deploy papkasi tayyorlanmoqda..."
STAGE="$ROOT/storage/deploy-stage"
rm -rf "$STAGE"
mkdir -p "$STAGE/formcraft" "$STAGE/forms"

# Laravel core → formcraft (public dan tashqari)
rsync -a \
  --exclude='.git' \
  --exclude='.env' \
  --exclude='.env.*' \
  --exclude='node_modules' \
  --exclude='storage/deploy-stage' \
  --exclude='storage/logs/*' \
  --exclude='storage/framework/cache/data/*' \
  --exclude='storage/framework/sessions/*' \
  --exclude='storage/framework/views/*' \
  --exclude='tests' \
  --exclude='public' \
  "$ROOT/" "$STAGE/formcraft/"

# public → forms
rsync -a "$ROOT/public/" "$STAGE/forms/"
cp "$ROOT/deploy/aqualime/index.php" "$STAGE/forms/index.php"
cp "$ROOT/deploy/aqualime/.htaccess" "$STAGE/forms/.htaccess"
cp "$ROOT/deploy/aqualime/formcraft.htaccess" "$STAGE/formcraft/.htaccess"

# Bo'sh storage papkalar
mkdir -p "$STAGE/formcraft/storage/app/public" \
         "$STAGE/formcraft/storage/framework/cache/data" \
         "$STAGE/formcraft/storage/framework/sessions" \
         "$STAGE/formcraft/storage/framework/views" \
         "$STAGE/formcraft/storage/logs" \
         "$STAGE/formcraft/bootstrap/cache"
touch "$STAGE/formcraft/storage/logs/.gitkeep"

echo "==> 3/5 FTP orqali yuklanmoqda ($HOST)..."
if ! command -v lftp &>/dev/null; then
  echo "lftp topilmadi. macOS: brew install lftp"
  exit 1
fi

lftp -u "$USER","$PASS" "$HOST" <<EOF
set ssl:verify-certificate no
set ftp:ssl-allow yes
mirror -R --delete --verbose "$STAGE/formcraft" public_html/formcraft
mirror -R --delete --verbose "$STAGE/forms" public_html/forms
bye
EOF

echo "==> 4/5 Yuklash tugadi."
echo ""
echo "==> 5/5 Serverda qo'lda bajarish (cPanel Terminal yoki SSH):"
echo ""
cat <<'INSTRUCTIONS'
cd ~/public_html/formcraft

# .env yarating (deploy/aqualime/.env.production.example dan nusxa)
cp .env.production.example .env   # yoki File Manager orqali
php artisan key:generate --force
# DB ma'lumotlarini cPanel → MySQL Databases dan kiriting

php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache

# forms/storage symlink (agar storage:link ishlamasa):
# ln -s ../formcraft/storage/app/public ~/public_html/forms/storage

chmod -R 775 storage bootstrap/cache
INSTRUCTIONS

echo ""
echo "Tekshirish: https://aqualime.uz/forms/login"
echo "Mavjud sayt: https://aqualime.uz/login/ (o'zgarmaydi)"
