#!/usr/bin/env bash
# Erzeugt eine statische Vorschau der j@m-Website (für GitHub Pages) aus dem
# Grav-Theme in grav/user/. Installiert dazu temporär einen echten Grav-Core
# (PHP), rendert alle Seiten und speichert sie als reines HTML in docs/.
#
# Das ist NUR eine read-only Vorschau der Beispiel-Inhalte, kein CMS/Admin-Panel.
# Für den echten Betrieb siehe grav/README.md (Installation auf eurem Webhosting).
#
# Voraussetzungen: php (>=8.1) mit gd/mbstring/zip, composer, wget.
#
# Aufruf: ./scripts/build-static-preview.sh

set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
BUILD_DIR="$(mktemp -d)"
PORT=8123

cleanup() {
    kill "${SERVER_PID:-0}" 2>/dev/null || true
    rm -rf "$BUILD_DIR"
}
trap cleanup EXIT

echo "==> Grav-Core wird in $BUILD_DIR installiert..."
composer create-project getgrav/grav "$BUILD_DIR" --no-interaction --prefer-dist --no-install
composer install --no-dev --no-interaction --working-dir="$BUILD_DIR"

echo "==> j@m-Theme, Blueprints und Beispiel-Inhalte einspielen..."
mkdir -p "$BUILD_DIR/user/themes/jam-theme" "$BUILD_DIR/user/blueprints/pages"
cp -r "$ROOT_DIR/grav/user/themes/jam-theme/." "$BUILD_DIR/user/themes/jam-theme/"
cp -r "$ROOT_DIR/grav/user/blueprints/pages/." "$BUILD_DIR/user/blueprints/pages/"
rm -rf "$BUILD_DIR/user/pages"
cp -r "$ROOT_DIR/grav/user/pages" "$BUILD_DIR/user/pages"
sed -i 's/theme: quark2/theme: jam-theme/' "$BUILD_DIR/user/config/system.yaml"

echo "==> Lokalen Server starten..."
(cd "$BUILD_DIR" && php -S "127.0.0.1:$PORT" system/router.php > /tmp/grav-preview-server.log 2>&1 &)
SERVER_PID=$!
sleep 2

echo "==> Seiten als statisches HTML spiegeln..."
SNAP_DIR="$(mktemp -d)"
(cd "$SNAP_DIR" && wget --mirror --convert-links --adjust-extension --page-requisites \
    --no-host-directories -e robots=off \
    "http://127.0.0.1:$PORT/" \
    "http://127.0.0.1:$PORT/neuigkeiten" \
    "http://127.0.0.1:$PORT/neuigkeiten/beispiel-neuigkeit" \
    "http://127.0.0.1:$PORT/termine" \
    "http://127.0.0.1:$PORT/termine/beispiel-termin" \
    "http://127.0.0.1:$PORT/ueber-uns")

echo "==> Hinweis-Banner einfügen..."
python3 - "$SNAP_DIR" <<'PYEOF'
import glob, sys
banner = '<div style="background:#2a1706;color:#fff;font:600 13px/1.4 -apple-system,Segoe UI,sans-serif;text-align:center;padding:.55rem 1rem;">Statische Vorschau (kein Admin-Panel, Beispiel-Inhalte) — echte Website läuft später über Grav CMS auf eurem Webhosting</div>'
for f in glob.glob(sys.argv[1] + '/**/*.html', recursive=True):
    content = open(f, encoding='utf-8').read()
    if 'Statische Vorschau' not in content:
        content = content.replace('<body>', '<body>\n' + banner, 1)
        open(f, 'w', encoding='utf-8').write(content)
PYEOF

echo "==> docs/ aktualisieren..."
rm -rf "$ROOT_DIR/docs"
mkdir -p "$ROOT_DIR/docs"
cp -r "$SNAP_DIR/." "$ROOT_DIR/docs/"
rm -rf "$SNAP_DIR"

echo "Fertig. Änderungen in docs/ committen und pushen, GitHub Pages aktualisiert sich automatisch."
