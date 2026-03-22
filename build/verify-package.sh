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
slug = 'kcharts'
main_file = f'{slug}/kcharts.php'
forbidden = ('KTEvents', 'kontentainment-events', 'charts-platform.php', 'kontentainment-charts', '.codex/', '__MACOSX', '.DS_Store')

with zipfile.ZipFile(zip_path) as archive:
    names = archive.namelist()
    roots = sorted({name.split('/')[0] for name in names if name})
    if roots != [slug]:
        raise SystemExit(f'Invalid ZIP root: {roots!r}')
    if main_file not in names:
        raise SystemExit(f'Missing main plugin file: {main_file}')
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
        'Text Domain: kcharts',
    )
    for needle in required:
        if needle not in header:
            raise SystemExit(f'Missing required header value: {needle}')

print('Package verification passed:', zip_path)
PY
