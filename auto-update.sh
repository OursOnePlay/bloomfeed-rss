#!/usr/bin/env bash
set -euo pipefail

# Checks whether the BloomFeed git repository has new commits on origin/main
# and, if so, redeploys via scriptsite.sh. Designed to be run periodically by
# a cron job installed with --install (see below). Only redeploys when there
# is actually something new, so an idle instance never rebuilds for nothing.

APP_DIR="/opt/bloomfeed"
CRON_FILE="/etc/cron.d/bloomfeed-auto-update"
LOG_DIR="${APP_DIR}/storage/logs"
LOG_FILE="${LOG_DIR}/auto-update.log"
INTERVAL_MINUTES=30
FORCE=false

log() {
  echo "$(date -Iseconds) $1"
}

case "${1:-}" in
  --install)
    INTERVAL_MINUTES="${2:-30}"
    if ! [[ "${INTERVAL_MINUTES}" =~ ^[0-9]+$ ]] || [[ "${INTERVAL_MINUTES}" -lt 1 || "${INTERVAL_MINUTES}" -gt 1440 ]]; then
      echo "ERROR: interval must be a number of minutes between 1 and 1440." >&2
      exit 1
    fi
    sudo mkdir -p "${LOG_DIR}"
    echo "*/${INTERVAL_MINUTES} * * * * root ${APP_DIR}/auto-update.sh >> ${LOG_FILE} 2>&1" | sudo tee "${CRON_FILE}" >/dev/null
    sudo chmod 644 "${CRON_FILE}"
    echo "==> Auto-update installed: checking for new commits every ${INTERVAL_MINUTES} minute(s)."
    echo "    Cron file: ${CRON_FILE}"
    echo "    Logs:      ${LOG_FILE}"
    exit 0
    ;;
  --uninstall)
    sudo rm -f "${CRON_FILE}"
    echo "==> Auto-update disabled (cron file removed)."
    exit 0
    ;;
  --status)
    if [[ -f "${CRON_FILE}" ]]; then
      echo "==> Auto-update is ENABLED:"
      cat "${CRON_FILE}"
    else
      echo "==> Auto-update is DISABLED (no ${CRON_FILE})."
    fi
    if [[ -f "${LOG_FILE}" ]]; then
      echo "==> Last log entries:"
      tail -n 10 "${LOG_FILE}"
    fi
    exit 0
    ;;
  --force)
    FORCE=true
    ;;
  --help|-h)
    echo "Usage: bash auto-update.sh [--install [minutes]|--uninstall|--status|--force]"
    echo "  (no args)        Check for new commits and redeploy if found - meant to be run by cron."
    echo "  --install [N]    Install a cron job checking every N minutes (default: 30)."
    echo "  --uninstall      Remove the cron job."
    echo "  --status         Show whether auto-update is enabled and recent log entries."
    echo "  --force          Redeploy immediately, even without a new commit."
    exit 0
    ;;
  "") ;;
  *)
    echo "Unknown option: ${1} (use --help)" >&2
    exit 1
    ;;
esac

if [[ ! -d "${APP_DIR}/.git" ]]; then
  echo "ERROR: ${APP_DIR} is not a git repository - run scriptsite.sh first." >&2
  exit 1
fi

mkdir -p "${LOG_DIR}"
cd "${APP_DIR}"

git fetch origin main --quiet

LOCAL_SHA="$(git rev-parse HEAD)"
REMOTE_SHA="$(git rev-parse origin/main)"

if [[ "${FORCE}" != true && "${LOCAL_SHA}" == "${REMOTE_SHA}" ]]; then
  log "No update available (up to date at ${LOCAL_SHA:0:7})."
  exit 0
fi

if [[ "${FORCE}" == true ]]; then
  log "Forced redeploy requested (currently at ${LOCAL_SHA:0:7})."
else
  log "Update found: ${LOCAL_SHA:0:7} -> ${REMOTE_SHA:0:7}. Redeploying..."
fi

if bash "${APP_DIR}/scriptsite.sh"; then
  log "Auto-update finished successfully (now at $(git rev-parse HEAD | cut -c1-7))."
else
  log "Auto-update FAILED - the instance may be left in a partially deployed state, check the output above."
  exit 1
fi
