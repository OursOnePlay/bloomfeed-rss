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
      echo "  --purge         Supprime aussi les volumes (base de données, storage)"
      echo "  --purge-source  Supprime aussi le code source et le .env dans ${APP_DIR} (nécessite --purge)"
      echo "  --yes           Ne pas demander de confirmation interactive"
      exit 0
      ;;
    *)
      echo "Option inconnue: $arg (utilisez --help)"
      exit 1
      ;;
  esac
done

if [[ ! -d "${APP_DIR}" ]]; then
  echo "ERREUR: ${APP_DIR} introuvable — rien à désinstaller."
  exit 1
fi

cd "${APP_DIR}"

echo "==> Arrêt et suppression des conteneurs BloomFeed"
sudo docker compose down --remove-orphans || true

if [[ "${PURGE_DATA}" == true ]]; then
  echo
  echo "ATTENTION: cette étape va supprimer DÉFINITIVEMENT :"
  echo "  - la base de données MariaDB (tous les comptes, flux, articles)"
  echo "  - les fichiers de storage de l'application"

  if [[ "${ASSUME_YES}" != true ]]; then
    read -r -p "Tapez SUPPRIMER pour confirmer : " CONFIRM
    if [[ "${CONFIRM}" != "SUPPRIMER" ]]; then
      echo "Annulé. Les volumes de données sont conservés."
      PURGE_DATA=false
    fi
  fi

  if [[ "${PURGE_DATA}" == true ]]; then
    echo "==> Suppression des volumes Docker"
    sudo docker volume rm \
      "${PROJECT_NAME}_db_data" \
      "${PROJECT_NAME}_app_storage" \
      "${PROJECT_NAME}_scheduler_storage" \
      2>/dev/null || true

    echo "==> Suppression des images Docker construites"
    sudo docker rmi "${PROJECT_NAME}-app" "${PROJECT_NAME}-scheduler" 2>/dev/null || true
  fi
else
  echo "==> Volumes de données conservés (relancez avec --purge pour les supprimer)"
fi

if [[ "${PURGE_SOURCE}" == true ]]; then
  echo
  echo "ATTENTION: cette étape va supprimer ${APP_DIR}, y compris le fichier .env (secrets, mots de passe)."

  if [[ "${ASSUME_YES}" != true ]]; then
    read -r -p "Tapez SUPPRIMER LE CODE pour confirmer : " CONFIRM_SRC
    if [[ "${CONFIRM_SRC}" != "SUPPRIMER LE CODE" ]]; then
      echo "Annulé. ${APP_DIR} est conservé."
      PURGE_SOURCE=false
    fi
  fi

  if [[ "${PURGE_SOURCE}" == true ]]; then
    cd /
    echo "==> Suppression de ${APP_DIR}"
    sudo rm -rf "${APP_DIR}"
  fi
else
  echo "==> Code source conservé dans ${APP_DIR} (relancez avec --purge-source pour le supprimer aussi)"
fi

echo
echo "==> Désinstallation terminée."
echo "    Conteneurs/réseau : supprimés"
echo "    Volumes de données : $([[ "${PURGE_DATA}" == true ]] && echo supprimés || echo conservés)"
echo "    Code source (${APP_DIR}) : $([[ "${PURGE_SOURCE}" == true ]] && echo supprimé || echo conservé)"
