#!/usr/bin/env bash
set -euo pipefail

APP_DIR="/opt/bloomfeed"
PROJECT_NAME="bloomfeed"

PURGE_DATA=false
PURGE_SOURCE=false
ASSUME_YES=false

for arg in "$@"; do
  case "$arg" in
    --purge) PURGE_DATA=true ;;
    --purge-source) PURGE_SOURCE=true ;;
    --yes|-y) ASSUME_YES=true ;;
    --help|-h)
      echo "Usage: bash uninstall.sh [--purge] [--purge-source] [--yes]"
      echo "  --purge         Also remove the volumes (database, storage)"
      echo "  --purge-source  Also remove the source code and the .env in ${APP_DIR}"
      echo "  --yes           Skip interactive confirmations"
      exit 0
      ;;
    *)
      echo "Unknown option: $arg (use --help)"
      exit 1
      ;;
  esac
done

if [[ ! -d "${APP_DIR}" ]]; then
  echo "ERROR: ${APP_DIR} not found — nothing to uninstall."
  exit 1
fi

cd "${APP_DIR}"

echo "==> Stopping and removing the BloomFeed containers"
sudo docker compose down --remove-orphans || true

if [[ "${PURGE_DATA}" == true ]]; then
  echo
  echo "WARNING: this step will PERMANENTLY delete:"
  echo "  - the MariaDB database (all accounts, feeds, articles)"
  echo "  - the application storage files"

  if [[ "${ASSUME_YES}" != true ]]; then
    read -r -p "Type DELETE to confirm: " CONFIRM
    if [[ "${CONFIRM}" != "DELETE" ]]; then
      echo "Cancelled. Data volumes are kept."
      PURGE_DATA=false
    fi
  fi

  if [[ "${PURGE_DATA}" == true ]]; then
    echo "==> Removing Docker volumes"
    sudo docker volume rm \
      "${PROJECT_NAME}_db_data" \
      "${PROJECT_NAME}_app_storage" \
      "${PROJECT_NAME}_scheduler_storage" \
      2>/dev/null || true

    echo "==> Removing built Docker images"
    sudo docker rmi "${PROJECT_NAME}-app" "${PROJECT_NAME}-scheduler" 2>/dev/null || true
  fi
else
  echo "==> Data volumes kept (re-run with --purge to remove them)"
fi

if [[ "${PURGE_SOURCE}" == true ]]; then
  echo
  echo "WARNING: this step will delete ${APP_DIR}, including the .env file (secrets, passwords)."

  if [[ "${ASSUME_YES}" != true ]]; then
    read -r -p "Type DELETE THE CODE to confirm: " CONFIRM_SRC
    if [[ "${CONFIRM_SRC}" != "DELETE THE CODE" ]]; then
      echo "Cancelled. ${APP_DIR} is kept."
      PURGE_SOURCE=false
    fi
  fi

  if [[ "${PURGE_SOURCE}" == true ]]; then
    cd /
    echo "==> Removing ${APP_DIR}"
    sudo rm -rf "${APP_DIR}"
  fi
else
  echo "==> Source code kept in ${APP_DIR} (re-run with --purge-source to remove it too)"
fi

echo
echo "==> Uninstall finished."
echo "    Containers/network : removed"
echo "    Data volumes       : $([[ "${PURGE_DATA}" == true ]] && echo removed || echo kept)"
echo "    Source (${APP_DIR}): $([[ "${PURGE_SOURCE}" == true ]] && echo removed || echo kept)"
