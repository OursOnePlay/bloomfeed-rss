/* ============================================================ */
/* BloomFluxDB — catalogue central de flux RSS pour les instances BloomFeed */
/* À exécuter sur le serveur catalogue (163.5.159.95), en root MariaDB/MySQL : */
/*   mysql -u root -p < bloomfluxdb-setup.sql */
/* Prérequis réseau : MariaDB doit écouter sur l'extérieur */
/* (bind-address = 0.0.0.0 dans /etc/mysql/mariadb.conf.d/50-server.cnf, */
/*  puis systemctl restart mariadb) et le port 3306 doit être ouvert. */
/* ============================================================ */

CREATE DATABASE IF NOT EXISTS bloomfluxdb
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE bloomfluxdb;

CREATE TABLE IF NOT EXISTS feeds (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    url VARCHAR(2048) NOT NULL,
    category VARCHAR(100) NOT NULL DEFAULT 'Divers',
    lang CHAR(2) NOT NULL DEFAULT 'fr',
    description VARCHAR(500) NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_feeds_url (url(500)),
    KEY idx_feeds_category (category),
    KEY idx_feeds_active (is_active)
) ENGINE = InnoDB;

/* ------------------------------------------------------------ */
/* Utilisateur en LECTURE SEULE pour les instances BloomFeed ('%' = accessible */
/* depuis n'importe quelle IP). Changez le mot de passe si vous voulez restreindre, */
/* et reportez-le dans le .env des instances (BLOOMFLUX_DB_PASSWORD). */
/* ------------------------------------------------------------ */
CREATE USER IF NOT EXISTS 'bloomflux_reader'@'%' IDENTIFIED BY 'BloomFlux_Public_Read_2026';
GRANT SELECT ON bloomfluxdb.feeds TO 'bloomflux_reader'@'%';
FLUSH PRIVILEGES;

/* ------------------------------------------------------------ */
/* Liste de flux pré-faite (extensible à volonté : INSERT depuis root/localhost) */
/* ------------------------------------------------------------ */
INSERT IGNORE INTO feeds (title, url, category, lang, description) VALUES
/* Actualités générales */
('Le Monde',                'https://www.lemonde.fr/rss/une.xml',                              'Actualités',    'fr', 'La une du journal Le Monde.'),
('France Info',             'https://www.franceinfo.fr/titres.rss',                            'Actualités',    'fr', 'Les titres de France Info en continu.'),
('Courrier International',  'https://www.courrierinternational.com/feed/all/rss.xml',          'Actualités',    'fr', 'L''actualité internationale vue par la presse étrangère.'),
('BBC News',                'https://feeds.bbci.co.uk/news/rss.xml',                           'Actualités',    'en', 'Fil d''actualité mondial de la BBC.'),
/* Tech & informatique */
('Korben',                  'https://korben.info/feed',                                        'Tech',          'fr', 'Actu tech, astuces et bidouilles.'),
('Numerama',                'https://www.numerama.com/feed/',                                  'Tech',          'fr', 'Société numérique, tech et sciences.'),
('Next',                    'https://next.ink/feed/',                                          'Tech',          'fr', 'Actualité informatique et numérique de fond.'),
('Journal du hacker',       'https://www.journalduhacker.net/rss',                             'Tech',          'fr', 'Liens tech partagés par la communauté francophone.'),
('Ars Technica',            'https://feeds.arstechnica.com/arstechnica/index',                 'Tech',          'en', 'Analyses tech approfondies.'),
('Hacker News',             'https://news.ycombinator.com/rss',                                'Tech',          'en', 'Les liens les plus discutés de la communauté HN.'),
('The Verge',               'https://www.theverge.com/rss/index.xml',                          'Tech',          'en', 'Tech, science et culture numérique.'),
/* Linux & open source */
('Phoronix',                'https://www.phoronix.com/rss.php',                                'Open source',   'en', 'Linux, matériel et open source.'),
('LinuxFr',                 'https://linuxfr.org/news.atom',                                   'Open source',   'fr', 'L''actualité du logiciel libre en français.'),
('It''s FOSS',              'https://itsfoss.com/rss/',                                        'Open source',   'en', 'Tutoriels et actus Linux/open source.'),
/* Sécurité */
('CERT-FR',                 'https://www.cert.ssi.gouv.fr/feed/',                              'Sécurité',      'fr', 'Alertes et avis de sécurité de l''ANSSI.'),
('Krebs on Security',       'https://krebsonsecurity.com/feed/',                               'Sécurité',      'en', 'Investigations cybersécurité de Brian Krebs.'),
('Zataz',                   'https://www.zataz.com/feed/',                                     'Sécurité',      'fr', 'Actualité cybersécurité et fuites de données.'),
/* Développement */
('Dev.to',                  'https://dev.to/feed',                                             'Développement', 'en', 'Articles de la communauté dev.to.'),
('GitHub Blog',             'https://github.blog/feed/',                                       'Développement', 'en', 'Nouveautés et ingénierie GitHub.'),
('Laravel News',            'https://feed.laravel-news.com/',                                  'Développement', 'en', 'L''écosystème Laravel au quotidien.'),
/* Sciences */
('Futura Sciences',         'https://www.futura-sciences.com/rss/actualites.xml',              'Sciences',      'fr', 'Actualités scientifiques tous domaines.'),
('CNRS Le Journal',         'https://lejournal.cnrs.fr/rss',                                   'Sciences',      'fr', 'Le journal de la recherche française.'),
/* Jeux vidéo */
('Gamekult',                'https://www.gamekult.com/feed.xml',                               'Jeux vidéo',    'fr', 'Actualité et critiques jeux vidéo.'),
('Rock Paper Shotgun',      'https://www.rockpapershotgun.com/feed',                           'Jeux vidéo',    'en', 'Jeux PC, tests et actus.');

/* Vérification rapide */
SELECT category, COUNT(*) AS nb FROM feeds GROUP BY category ORDER BY category;
