#!/bin/sh
set -eu

SCRIPT_DIR="$(CDPATH= cd -- "$(dirname "$0")" && pwd)"
PLUGIN_ROOT="$(CDPATH= cd -- "$SCRIPT_DIR/.." && pwd)"
SLUG="kontentainment-charts"
MAIN_FILE="kontentainment-charts.php"
BUILD_DIR="$PLUGIN_ROOT/.build/$SLUG"
STAGE_DIR="$BUILD_DIR/$SLUG"
DIST_DIR="$PLUGIN_ROOT/dist"
ZIP_PATH="$DIST_DIR/$SLUG.zip"

rm -rf "$BUILD_DIR"
mkdir -p "$STAGE_DIR" "$DIST_DIR"

copy_path() {
  src="$1"
  if [ -e "$PLUGIN_ROOT/$src" ]; then
    mkdir -p "$STAGE_DIR/$(dirname "$src")"
    cp -R "$PLUGIN_ROOT/$src" "$STAGE_DIR/$src"
  fi
}

copy_path "$MAIN_FILE"
copy_path "index.php"
copy_path "readme.txt"
copy_path "assets"
copy_path "includes"
copy_path "templates"

find "$STAGE_DIR" \
  \( -name '.DS_Store' -o -name '.git' -o -name '.gitignore' -o -name '.gitattributes' -o -name '.codex' \) \
  -print0 | xargs -0 rm -rf 2>/dev/null || true

cd "$BUILD_DIR"
rm -f "$ZIP_PATH"
zip -qr "$ZIP_PATH" "$SLUG"

"$SCRIPT_DIR/verify-package.sh" "$ZIP_PATH"
echo "$ZIP_PATH"
