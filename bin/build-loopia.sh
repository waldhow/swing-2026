#!/usr/bin/env bash
# Bygger ett uppladdningspaket för Loopia. Driftsätts med bin/deploy-loopia.sh
# (SSH/rsync) eller laddas upp med SFTP. Resultat i dist/loopia/:
#
#   public_html/   → laddas upp till domänens webbrot (t.ex. /swinggolf.se/public_html/)
#   swing-app/     → laddas upp BREDVID webbroten (t.ex. /swinggolf.se/swing-app/),
#                    så att kod, .env och loggar aldrig kan nås via webben
#
# Användning: bin/build-loopia.sh            (appmapp: swing-app)
#             APPDIR=annat-namn bin/build-loopia.sh
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
APPDIR="${APPDIR:-swing-app}"
OUT="$ROOT/dist/loopia"

echo "→ Rensar $OUT"
rm -rf "$OUT"
mkdir -p "$OUT/public_html" "$OUT/$APPDIR"

echo "→ Kopierar app och Composer-filer"
rsync -a --exclude '.DS_Store' "$ROOT/app" "$OUT/$APPDIR/"
cp "$ROOT/composer.json" "$ROOT/composer.lock" "$ROOT/spark" "$OUT/$APPDIR/"
# spark letar efter public/ – på servern heter webbroten public_html/ och ligger bredvid appmappen.
perl -pi -e "s#__DIR__ \. DIRECTORY_SEPARATOR \. 'public' \. DIRECTORY_SEPARATOR#__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'public_html' . DIRECTORY_SEPARATOR#" "$OUT/$APPDIR/spark"
perl -pi -e "s#require FCPATH \. '\.\./app/Config/Paths\.php';#require __DIR__ . '/app/Config/Paths.php';#" "$OUT/$APPDIR/spark"
grep -q "'public_html'" "$OUT/$APPDIR/spark" && grep -q "require __DIR__ . '/app/Config/Paths.php';" "$OUT/$APPDIR/spark" \
  || { echo "Kunde inte peka spark på public_html och appmappen" >&2; exit 1; }
cp "$ROOT/.env.example" "$OUT/$APPDIR/.env.example"

echo "→ Installerar produktionsberoenden (utan dev-paket)"
composer install --working-dir="$OUT/$APPDIR" --no-dev --optimize-autoloader --no-interaction --no-progress --quiet

echo "→ Skapar writable/"
for dir in cache logs session uploads debugbar; do
  mkdir -p "$OUT/$APPDIR/writable/$dir"
  cp "$ROOT/writable/index.html" "$OUT/$APPDIR/writable/$dir/index.html"
done
cp "$ROOT/writable/index.html" "$ROOT/writable/.htaccess" "$OUT/$APPDIR/writable/"

# Om appmappen ändå hamnar i webbroten: neka all åtkomst.
printf 'Require all denied\n' > "$OUT/$APPDIR/.htaccess"

echo "→ Kopierar public/ till public_html/"
rsync -a --exclude '.DS_Store' --exclude 'assets/photos/' "$ROOT/public/" "$OUT/public_html/"

# Front controllern pekar på appmappen bredvid webbroten.
perl -pi -e "s#require FCPATH \. '\.\./app/Config/Paths\.php';#require FCPATH . '../$APPDIR/app/Config/Paths.php';#" "$OUT/public_html/index.php"
grep -q "../$APPDIR/app/Config/Paths.php" "$OUT/public_html/index.php" || { echo "Kunde inte peka index.php på $APPDIR" >&2; exit 1; }

echo "→ Kontrollerar syntax"
find "$OUT/$APPDIR/app" -name '*.php' -print0 | xargs -0 -n 1 -P 4 php -l > /dev/null

echo
echo "Klart: $OUT"
echo "  1. Ladda upp public_html/* till webbroten och $APPDIR/ bredvid den."
echo "  2. Skapa $APPDIR/.env från .env.example (första gången)."
echo "  3. Kör /admin/setup/?token=… för migrationer. Se docs/drift-loopia.md."
