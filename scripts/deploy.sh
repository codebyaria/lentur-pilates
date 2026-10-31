#!/usr/bin/env bash
# Rilis main ke lentur.arizain.my.id (server yang sama dengan Alon Cake dan
# arizain.my.id, lihat DEPLOY.md). Server tidak punya Node, jadi kode dikirim
# sebagai arsip dari commit HEAD dan frontend di-build di Mac ini.
#
# Pemakaian: scripts/deploy.sh   (butuh host "alon-vps" di ~/.ssh/config)
set -euo pipefail

HOST=alon-vps
APP=/var/www/lentur
URL=https://lentur.arizain.my.id
ROOT=$(cd "$(dirname "$0")/.." && pwd)
TMP=$(mktemp -d)
trap 'rm -rf "$TMP"; [ -f "$TMP.hot" ] && mv "$TMP.hot" "$ROOT/public/hot"' EXIT

cd "$ROOT"
if [ -n "$(git status --porcelain)" ]; then
    echo "Working tree belum bersih; commit dulu." >&2
    exit 1
fi

echo "→ Arsip $(git rev-parse --short HEAD)"
git archive --format=tar.gz -o "$TMP/app.tar.gz" HEAD

echo "→ Build frontend"
[ -f public/hot ] && mv public/hot "$TMP.hot"
npm run build >/dev/null
COPYFILE_DISABLE=1 tar -czf "$TMP/build.tar.gz" -C public build
rm -rf public/build

echo "→ Unggah"
scp -q "$TMP/app.tar.gz" "$TMP/build.tar.gz" "$HOST:/tmp/"

echo "→ Pasang di server"
ssh "$HOST" "set -e; cd $APP
tar -xzf /tmp/app.tar.gz 2>/dev/null
rm -rf public/build && tar -xzf /tmp/build.tar.gz -C public 2>/dev/null
rm /tmp/app.tar.gz /tmp/build.tar.gz
COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --optimize-autoloader --no-interaction -q
php artisan migrate --force
chown -R www-data:www-data storage bootstrap/cache database
php artisan optimize >/dev/null"

echo "→ Cek"
for path in / /en /booking /admin/login; do
    curl -s -o /dev/null -w "$URL$path → %{http_code}\n" "$URL$path"
done
