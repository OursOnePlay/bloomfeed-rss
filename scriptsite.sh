#!/usr/bin/env bash
set -euo pipefail

REPO_URL="https://git.eldorianet.work/esteban/BloomFeed-RSS"
APP_DIR="/opt/bloomfeed"
HOST_PORT="7081"

echo "==> Kill forcé de tout process qui écoute sur le port ${HOST_PORT}"
PIDS="$(sudo lsof -ti tcp:${HOST_PORT} || true)"
if [[ -n "${PIDS}" ]]; then
  echo "PIDs trouvés sur ${HOST_PORT}: ${PIDS}"
  sudo kill -9 ${PIDS} || true
else
  echo "Aucun process actif sur ${HOST_PORT}"
fi

if [[ -d "${APP_DIR}/.git" ]]; then
  echo "==> Mise à jour du dépôt existant"
  sudo git -C "${APP_DIR}" remote set-url origin "${REPO_URL}"
  sudo git -C "${APP_DIR}" fetch --all
  sudo git -C "${APP_DIR}" reset --hard origin/main
else
  echo "==> Suppression complète de ${APP_DIR} et clone propre du dépôt"
  sudo rm -rf "${APP_DIR}"
  sudo mkdir -p "${APP_DIR}"
  sudo git clone "${REPO_URL}" "${APP_DIR}"
fi

cd "${APP_DIR}"

if [[ ! -f ".env" ]]; then
  echo "==> Aucun .env trouvé, création à partir de .env.example"
  cp .env.example .env
fi

echo "==> Génération des secrets manquants dans .env (ne régénère jamais un secret déjà présent)"
if grep -q '^APP_KEY=$' .env; then
  sed -i "s#^APP_KEY=.*#APP_KEY=base64:$(openssl rand -base64 32)#" .env
  echo "  - APP_KEY généré"
fi
if grep -q '^DB_PASSWORD=$' .env; then
  sed -i "s#^DB_PASSWORD=.*#DB_PASSWORD=$(openssl rand -hex 24)#" .env
  echo "  - DB_PASSWORD généré"
fi
if grep -q '^DB_ROOT_PASSWORD=$' .env; then
  sed -i "s#^DB_ROOT_PASSWORD=.*#DB_ROOT_PASSWORD=$(openssl rand -hex 24)#" .env
  echo "  - DB_ROOT_PASSWORD généré"
fi
chmod 600 .env

echo "==> Arrêt de l'ancienne stack Docker Compose si présente"
sudo docker compose down --remove-orphans || true

echo "==> Build des images Docker (app + scheduler)"
sudo docker compose build --pull

echo "==> Lancement de la stack (app, scheduler, db)"
sudo docker compose up -d

echo "==> Attente de la disponibilité de la base de données"
for i in $(seq 1 30); do
  if sudo docker compose exec -T db healthcheck.sh --connect --innodb_initialized >/dev/null 2>&1; then
    echo "==> Base de données prête"
    break
  fi
  echo "==> DB pas encore prête, nouvelle tentative ($i/30)"
  sleep 3
done

# NOTE : ne PAS lancer "artisan migrate" ici — l'entrypoint du conteneur app le fait
# déjà au démarrage, et deux migrations concurrentes corrompent le registre des
# migrations ("table already exists").
echo "==> Attente de la fin des migrations de l'entrypoint"
for i in $(seq 1 30); do
  if curl -sf -o /dev/null "http://localhost:${HOST_PORT}/up"; then
    echo "==> Application démarrée"
    break
  fi
  echo "==> App pas encore prête, nouvelle tentative ($i/30)"
  sleep 3
done

echo "==> Vérification des conteneurs"
sudo docker compose ps

echo "==> Vérification du port ${HOST_PORT}"
sudo lsof -nP -iTCP:${HOST_PORT} -sTCP:LISTEN || true

echo "==> Test HTTP de l'application"
curl -sf -o /dev/null -w "HTTP %{http_code}\n" "http://localhost:${HOST_PORT}/login" || echo "L'application ne répond pas encore, vérifiez les logs."

echo "==> Logs récents"
sudo docker compose logs --tail 50 app || true

echo "==> Déploiement BloomFeed terminé — http://<ip-serveur>:${HOST_PORT}"
