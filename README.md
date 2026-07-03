<p align="center"><img src="public/images/logo.png" width="360" alt="BloomFeed"></p>

<p align="center">
Un agrégateur RSS personnel et auto-hébergé, avec lecture approfondie intégrée.
</p>

<p align="center"><img src="docs/screenshots/home-light.png" width="800" alt="Page d'accueil de BloomFeed"></p>

## Qu'est-ce que BloomFeed ?

BloomFeed centralise vos flux RSS/Atom préférés dans une interface simple. Quand un article vous
intéresse, BloomFeed récupère la page source, en extrait le contenu principal et l'affiche
directement dans l'application — plus besoin d'ouvrir le site d'origine.

BloomFeed est pensé pour être **auto-hébergé par une seule personne ou un foyer** : une instance
n'a qu'un seul compte propriétaire, créé une seule fois au premier lancement.

## Fonctionnalités

- Ajout de flux RSS/Atom, suivi des non-lus, favoris.
- Lecture approfondie intégrée : extraction du contenu principal (via
  [Readability](https://github.com/fivefilters/readability.php)) converti en Markdown, sans
  quitter BloomFeed.
- Catalogue de flux façon "market" alimenté par **BloomFluxDB** (base centrale en lecture seule),
  avec cache local de secours si le serveur catalogue est injoignable.
- Récupération périodique des flux via une tâche planifiée.
- Thème clair/sombre.
- Inscription fermée après la création du compte propriétaire — pas de SaaS ouvert.

## Aperçu

| Tableau de bord | Lecteur intégré |
|---|---|
| ![Tableau de bord](docs/screenshots/dashboard-light.png) | ![Lecteur intégré](docs/screenshots/reader-light.png) |

| Catalogue de flux | Mode sombre |
|---|---|
| ![Catalogue de flux](docs/screenshots/catalog-light.png) | ![Mode sombre](docs/screenshots/dashboard-dark.png) |

## Stack technique

- [Laravel 13](https://laravel.com) (PHP 8.4)
- MariaDB
- CSS/JS écrits à la main, sans étape de build (pas de Node/Vite/Tailwind)
- Docker Compose pour le déploiement

## Développement local

Prérequis : PHP 8.3+ et [Composer](https://getcomposer.org).

```bash
composer install
cp .env.example .env
# Passer DB_CONNECTION=sqlite dans .env pour un dev local sans MariaDB
touch database/database.sqlite
php artisan key:generate
php artisan migrate
php artisan serve
```

## Auto-hébergement

BloomFeed est fait pour tourner sur votre propre serveur via Docker Compose (application,
planificateur, MariaDB). Voir la page **[/self-hosting](/self-hosting)** une fois
l'application lancée, ou directement :

```bash
git clone https://git.eldorianet.work/esteban/BloomFeed-RSS bloomfeed
cd bloomfeed
bash scriptsite.sh
```

Le script crée automatiquement un `.env` avec des secrets générés aléatoirement s'il n'existe
pas encore, construit les images et démarre la stack. L'application écoute par défaut sur le
port `7081`.

Pour désinstaller proprement (avec confirmation avant toute suppression de données) :

```bash
bash uninstall.sh
```

## BloomFluxDB (catalogue central)

Les instances BloomFeed consultent en lecture seule une base MariaDB centrale contenant une
liste de flux pré-sélectionnés, affichée dans l'onglet **Catalogue**. La liste est mise en cache
localement : si le serveur catalogue est injoignable, la dernière copie connue est servie, et
elle se rafraîchit automatiquement à la prochaine consultation réussie.

Le script de création (base, table, utilisateur `bloomflux_reader` en `SELECT` uniquement, et
liste de flux initiale) est dans [docs/bloomfluxdb-setup.sql](docs/bloomfluxdb-setup.sql) — à
exécuter sur le serveur catalogue :

```bash
mysql -u root -p < docs/bloomfluxdb-setup.sql
```

La connexion se configure dans le `.env` de chaque instance via les variables `BLOOMFLUX_DB_*`.

## Licence

MIT.
