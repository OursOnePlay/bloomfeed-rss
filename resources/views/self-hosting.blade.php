@extends('layouts.marketing', ['title' => 'Auto-héberger BloomFeed'])

@section('content')
<section class="docs-shell">
    <h1>Auto-héberger BloomFeed</h1>
    <p class="hero-lede">
        BloomFeed est pensé pour être installé sur votre propre serveur, pour vous (ou votre foyer).
        Une instance = un seul compte propriétaire. Tout tourne dans Docker : l'application
        et la base de données MariaDB.
    </p>

    <div class="card mt-3">
        <h2>Prérequis</h2>
        <ul class="docs-list">
            <li>Un serveur Linux avec <strong>Docker</strong> et le plugin <strong>Docker Compose</strong>.</li>
            <li>Le port <strong>7081</strong> libre (modifiable dans <code>docker-compose.yml</code>).</li>
            <li>Optionnel : un nom de domaine et un reverse proxy si vous voulez du HTTPS.</li>
        </ul>
    </div>

    <div class="card mt-2">
        <h2>Installation</h2>
        <ol class="docs-list docs-list-numbered">
            <li>Clonez le dépôt sur votre serveur.</li>
            <li>
                Lancez le script de déploiement :
                <pre class="code-block">bash scriptsite.sh</pre>
                Il crée automatiquement un fichier <code>.env</code> (à partir de <code>.env.example</code>) et y génère
                des secrets aléatoires (<code>APP_KEY</code>, mots de passe de base de données) s'ils sont absents —
                ils ne sont jamais régénérés lors des déploiements suivants.
            </li>
            <li>Le script construit les images et démarre les conteneurs (application, planificateur, MariaDB).</li>
            <li>Ouvrez <code>http://votre-serveur:7081</code> et créez le compte propriétaire — cette option disparaît ensuite définitivement.</li>
        </ol>
    </div>

    <div class="card mt-2">
        <h2>Mettre à jour</h2>
        <p>Relancez simplement <code>bash scriptsite.sh</code> : il récupère les derniers changements et reconstruit la stack sans toucher à vos données.</p>
    </div>

    <div class="card mt-2">
        <h2>Désinstaller</h2>
        <p>
            Le script <code>uninstall.sh</code> arrête et supprime proprement la stack Docker.
            Il demande une confirmation explicite avant de supprimer vos données (base de données)
            et ne supprime jamais le code source sans confirmation séparée.
        </p>
    </div>

    <div class="card mt-2">
        <h2>Code source</h2>
        <p>
            <a href="https://git.eldorianet.work/esteban/BloomFeed-RSS" target="_blank" rel="noopener">git.eldorianet.work/esteban/BloomFeed-RSS</a>
        </p>
    </div>
</section>
@endsection
