#!/usr/bin/env bash
set -euo pipefail

REPO_URL="https://git.eldorianet.work/esteban/BloomFeed-RSS"
APP_DIR="/opt/bloomfeed"
HOST_PORT="7081"
BACKUP_RETENTION="10"

echo "==> Killing any process listening on port ${HOST_PORT}"
PIDS="$(sudo lsof -ti tcp:${HOST_PORT} || true)"
if [[ -n "${PIDS}" ]]; then
  echo "PIDs found on ${HOST_PORT}: ${PIDS}"
  sudo kill -9 ${PIDS} || true
else
  echo "No active process on ${HOST_PORT}"
fi

if [[ -d "${APP_DIR}/.git" ]]; then
  echo "==> Updating existing repository"
  sudo git -C "${APP_DIR}" remote set-url origin "${REPO_URL}"
  sudo git -C "${APP_DIR}" fetch --all
  sudo git -C "${APP_DIR}" reset --hard origin/main
else
  echo "==> Removing ${APP_DIR} and cloning a fresh copy"
  sudo rm -rf "${APP_DIR}"
  sudo mkdir -p "${APP_DIR}"
  sudo git clone "${REPO_URL}" "${APP_DIR}"
fi

cd "${APP_DIR}"

if [[ ! -f ".env" ]]; then
  echo "==> No .env found, creating one from .env.example"
  cp .env.example .env
fi

echo "==> Generating missing secrets in .env (existing secrets are never regenerated)"
if grep -q '^APP_KEY=$' .env; then
  sed -i "s#^APP_KEY=.*#APP_KEY=base64:$(openssl rand -base64 32)#" .env
  echo "  - APP_KEY generated"
fi
if grep -q '^DB_PASSWORD=$' .env; then
  sed -i "s#^DB_PASSWORD=.*#DB_PASSWORD=$(openssl rand -hex 24)#" .env
  echo "  - DB_PASSWORD generated"
fi
if grep -q '^DB_ROOT_PASSWORD=$' .env; then
  sed -i "s#^DB_ROOT_PASSWORD=.*#DB_ROOT_PASSWORD=$(openssl rand -hex 24)#" .env
  echo "  - DB_ROOT_PASSWORD generated"
fi
chmod 600 .env

echo "==> Backing up the database before redeploying (if a previous instance is running)"
BACKUP_DIR="${APP_DIR}/backups"
if [[ -n "$(sudo docker compose ps -q db 2>/dev/null)" ]]; then
  sudo mkdir -p "${BACKUP_DIR}"
  BACKUP_FILE="${BACKUP_DIR}/bloomfeed-$(date +%Y%m%d-%H%M%S).sql.gz"
  # shellcheck disable=SC2016
  if sudo docker compose exec -T db sh -c 'exec mariadb-dump -u root -p"$MARIADB_ROOT_PASSWORD" --all-databases' | gzip > "${BACKUP_FILE}"; then
    sudo chmod 600 "${BACKUP_FILE}"
    echo "==> Backup saved: ${BACKUP_FILE}"
    echo "==> Pruning old backups (keeping the last ${BACKUP_RETENTION})"
    (sudo find "${BACKUP_DIR}" -maxdepth 1 -name 'bloomfeed-*.sql.gz' -print | sort | head -n -"${BACKUP_RETENTION}" | xargs -r sudo rm -f) || echo "WARNING: pruning old backups failed (non-fatal, continuing) — check ${BACKUP_DIR} manually." >&2
  else
    sudo rm -f "${BACKUP_FILE}"
    echo "ERROR: database backup failed — aborting the update so no data is put at risk." >&2
    echo "        Check 'sudo docker compose logs db' and retry manually." >&2
    exit 1
  fi
else
  echo "==> No running database container found (fresh install) — skipping backup"
fi

echo "==> Stopping the previous Docker Compose stack if present"
sudo docker compose down --remove-orphans || true

echo "==> Building the Docker images (app + scheduler)"
sudo docker compose build --pull

echo "==> Starting the stack (app, scheduler, db)"
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

# NOTE: do NOT run "artisan migrate" here — the app container entrypoint already
# runs it at startup, and two concurrent migrations corrupt the migrations
# ledger ("table already exists").
echo "==> Waiting for the entrypoint migrations to finish"
for i in $(seq 1 30); do
  if curl -sf -o /dev/null "http://localhost:${HOST_PORT}/up"; then
    echo "==> Application up"
    break
  fi
  echo "==> Application not ready yet, retrying ($i/30)"
  sleep 3
done

echo "==> Container status"
sudo docker compose ps

echo "==> Checking port ${HOST_PORT}"
sudo lsof -nP -iTCP:${HOST_PORT} -sTCP:LISTEN || true

echo "==> HTTP check"
curl -sf -o /dev/null -w "HTTP %{http_code}\n" "http://localhost:${HOST_PORT}/login" || echo "The application is not responding yet, check the logs."

echo "==> Recent logs"
sudo docker compose logs --tail 50 app || true

echo "==> BloomFeed deployment finished — http://<server-ip>:${HOST_PORT}"
