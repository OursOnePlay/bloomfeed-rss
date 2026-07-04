#!/usr/bin/env bash
set -euo pipefail

# Resets a BloomFeed instance to a fresh, empty state: wipes the database and
# application storage, then restarts the stack so the first user to register
# becomes the new owner. Source code, .env and Docker images are untouched —
# use uninstall.sh instead if you want to remove those too.

APP_DIR="/opt/bloomfeed"
PROJECT_NAME="bloomfeed"

ASSUME_YES=false

for arg in "$@"; do
  case "$arg" in
    --yes|-y) ASSUME_YES=true ;;
    --help|-h)
      echo "Usage: bash reset.sh [--yes]"
      echo "  --yes  Skip the interactive confirmation"
      exit 0
      ;;
    *)
      echo "Unknown option: $arg (use --help)"
      exit 1
      ;;
  esac
done

if [[ ! -d "${APP_DIR}" ]]; then
  echo "ERROR: ${APP_DIR} not found — nothing to reset."
  exit 1
fi

cd "${APP_DIR}"

echo "WARNING: this will PERMANENTLY delete all data on this instance:"
echo "  - the MariaDB database (all accounts, feeds, articles, notifiers)"
echo "  - the application storage files"
echo "The source code, .env and Docker images are kept."

if [[ "${ASSUME_YES}" != true ]]; then
  read -r -p "Type RESET to confirm: " CONFIRM
  if [[ "${CONFIRM}" != "RESET" ]]; then
    echo "Cancelled. Nothing was changed."
    exit 0
  fi
fi

echo "==> Stopping the stack"
sudo docker compose down --remove-orphans

echo "==> Removing data volumes"
sudo docker volume rm \
  "${PROJECT_NAME}_db_data" \
  "${PROJECT_NAME}_app_storage" \
  "${PROJECT_NAME}_scheduler_storage" \
  2>/dev/null || true

echo "==> Starting a fresh stack"
sudo docker compose up -d

echo "==> Waiting for the database to be ready"
for i in $(seq 1 30); do
  if sudo docker compose exec -T db healthcheck.sh --connect --innodb_initialized >/dev/null 2>&1; then
    echo "==> Database ready"
    break
  fi
  echo "==> Database not ready yet, retrying ($i/30)"
  sleep 3
done

echo "==> Waiting for the entrypoint migrations to finish"
for i in $(seq 1 30); do
  if curl -sf -o /dev/null "http://localhost:7081/up"; then
    echo "==> Application up"
    break
  fi
  echo "==> Application not ready yet, retrying ($i/30)"
  sleep 3
done

echo
echo "==> Reset finished. Visit the instance and create the new owner account."
