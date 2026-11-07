#!/usr/bin/env bash
set -euo pipefail

# Forces an immediate redeploy of BloomFeed, regardless of whether a new
# commit is actually available. Thin wrapper around auto-update.sh --force
# so the intent is explicit and easy to remember/document.

APP_DIR="/opt/bloomfeed"

if [[ ! -f "${APP_DIR}/auto-update.sh" ]]; then
  echo "ERROR: ${APP_DIR}/auto-update.sh not found - run scriptsite.sh first." >&2
  exit 1
fi

echo "==> Forcing an immediate BloomFeed update/redeploy..."
exec bash "${APP_DIR}/auto-update.sh" --force
