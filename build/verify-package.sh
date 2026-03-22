#!/bin/sh
set -eu

ZIP_PATH="${1:-}"

if [ -z "$ZIP_PATH" ] || [ ! -f "$ZIP_PATH" ]; then
  echo "Package file not found." >&2
  exit 1
fi

python3 - "$ZIP_PATH" <<'PY'
import sys, zipfile

zip_path = sys.argv[1]
slug = 'kontentainment-charts'
main_file = f'{slug}/kontentainment-charts.php'
forbidden = ('KTEvents', 'kontentainment-events', 'charts-platform.php', 'kcharts', '.codex/', '__MACOSX', '.DS_Store')

with zipfile.ZipFile(zip_path) as archive:
    names = archive.namelist()
    roots = sorted({name.split('/')[0] for name in names if name})
    if roots != [slug]:
        raise SystemExit(f'Failed package build: ZIP root folder is not {slug}/ (found {roots!r})')
    if main_file not in names:
        raise SystemExit(f'Failed package build: Main plugin file is not {main_file}')
    
    # check if basename and slug are correct
    if f"{slug}/{slug}.php" != main_file:
        raise SystemExit(f'Failed package build: Plugin basename/slug do not match {slug}')
    plugin_headers = []
    for name in names:
        if any(token in name for token in forbidden):
            raise SystemExit(f'Forbidden package entry found: {name}')
        if name.endswith('.php'):
            data = archive.read(name).decode('utf-8', 'ignore')
            if 'Plugin Name:' in data[:2048]:
                plugin_headers.append(name)
    if plugin_headers != [main_file]:
        raise SystemExit(f'Unexpected plugin headers found: {plugin_headers!r}')

    header = archive.read(main_file).decode('utf-8', 'ignore')
    required = (
        'Plugin Name: Kontentainment Charts',
        'Text Domain: kontentainment-charts',
    )
    for needle in required:
        if needle not in header:
            raise SystemExit(f'Missing required header value: {needle}')

print('Package verification passed:', zip_path)
PY
