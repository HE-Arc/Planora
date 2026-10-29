# Planora

Application Laravel de gestion d'événements. Ce guide couvre l'installation sur **Windows avec PowerShell et Laravel Herd**, la reprise du développement et les étapes applicatives d'un déploiement.

Exécuter les commandes dans l'ordre, **une étape à la fois**, et corriger toute erreur avant de continuer. Les commandes de ce guide sont pour PowerShell, sauf indication contraire.

## 1. Comprendre les dossiers

Le dépôt contient un sous-dossier du même nom :

```text
Planora/                         <- racine Git, ce README
├── scripts/
│   └── windows/
│       ├── build-frontend.ps1
│       └── build-frontend.bat
└── Planora/                     <- racine de l'application Laravel
    ├── artisan
    ├── composer.json
    ├── composer.lock
    ├── package.json
    ├── package-lock.json
    ├── .env.example
    ├── database/
    ├── resources/
    ├── storage/
    └── public/
```

Les commandes `php artisan`, `composer` et `npm` se lancent dans **le dossier qui contient `artisan`**. Les scripts Windows retrouvent ce dossier à partir de leur propre emplacement.

## 2. Installer les prérequis

| Outil | Version / rôle |
| --- | --- |
| Git pour Windows | Cloner le dépôt et travailler sur une branche. |
| Laravel Herd | Fournir PHP et un serveur local ; inclut les outils de gestion de Node.js. |
| PHP | Dernier correctif disponible de **8.4 ou 8.5**, avec **8.4.1 minimum**. |
| Composer | Version **2**, pour les dépendances PHP. |
| Node.js et npm | **Node.js 24 LTS recommandé** ; les scripts demandent au minimum **22.13.0**. npm est fourni avec Node.js. |
| PowerShell / CMD | Windows PowerShell **5.1** ou PowerShell **7** pour le `.ps1` ; CMD pour le `.bat`. |

Le `composer.lock` exige une version plus récente que le `^8.3` indiqué dans `composer.json` : les dépendances Symfony verrouillées nécessitent PHP 8.4.1, y compris sans les dépendances de développement. Certaines dépendances verrouillées limitent aussi PHP à 8.5 : ne pas choisir PHP 8.6 pour cette installation.

Installer [Git pour Windows](https://git-scm.com/downloads/win) et [Laravel Herd](https://herd.laravel.com/docs/windows/getting-started/installation). Dans les réglages de Herd, installer et sélectionner PHP 8.4 ou 8.5 à jour. Vérifier également la version PHP affectée au site, qui peut différer de celle du terminal. Voir la [gestion des versions PHP dans Herd](https://herd.laravel.com/docs/windows/technology/php-versions).

Pour Node.js, utiliser l'onglet JavaScript de Herd ou son gestionnaire `nvm` :

```powershell
nvm install 24
nvm use 24
```

Rouvrir le terminal après l'installation. Voir la [gestion de Node.js dans Herd](https://herd.laravel.com/docs/windows/technology/node-versions).

Vérifier les outils :

```powershell
git --version
php -v
composer --version
node --version
npm.cmd --version
php -m
```

PHP doit notamment disposer de **PDO et pdo_sqlite**, ainsi que des extensions demandées par Composer, dont `mbstring`, `openssl`, `fileinfo`, `dom` et les extensions XML pour les outils de développement. La vérification Composer de l'étape 5 donnera la liste exacte des éléments manquants.

Sous PowerShell, `npm.cmd` permet d'utiliser npm sans dépendre de l'autorisation d'exécuter son lanceur `npm.ps1`.

## 3. Récupérer le projet

Depuis le dossier où stocker les projets :

```powershell
git clone https://github.com/HE-Arc/Planora.git
Set-Location .\Planora
```

Pour reprendre le développement, partir de la branche d'intégration `Dev`, en respectant sa casse, puis créer une branche de travail. Remplacer `feature/ma-modification` par un nom adapté :

```powershell
git switch Dev
git pull --ff-only
git switch -c feature/ma-modification
```

Pour simplement lancer la version publiée, rester sur `main`. Sur un clone existant, vérifier `git status` et préserver son travail avant de changer de branche ou de récupérer des modifications.

Entrer ensuite dans l'application :

```powershell
Set-Location .\Planora
Test-Path .\artisan
```

La dernière commande doit retourner `True`. **Toutes les étapes suivantes partent de ce dossier Laravel**, sauf indication contraire.

> État actuel du dépôt : des fichiers de `vendor` et `node_modules` sont encore suivis par Git. Une installation peut donc faire apparaître de nombreuses différences générées. Vérifier `git status` après installation et sélectionner uniquement ses fichiers sources pour un commit ; éviter `git add .`. Leur nettoyage dans Git est une opération distincte de l'installation.

## 4. Préparer les fichiers locaux

Créer `.env` seulement s'il n'existe pas déjà :

```powershell
if (-not (Test-Path -LiteralPath .\.env)) {
    Copy-Item -LiteralPath .\.env.example -Destination .\.env
}
```

Ouvrir `.env` dans l'éditeur et adapter ces valeurs pour Herd :

```dotenv
APP_NAME=Planora
APP_ENV=local
APP_DEBUG=true
APP_URL=http://planora.test

DB_CONNECTION=sqlite
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

Conserver les autres paramètres de `.env.example`. **Ne pas remplacer une `APP_KEY` existante** ; sur un nouvel environnement, sa valeur reste vide jusqu'à l'étape suivante. `.env` contient la configuration privée du poste et ne doit pas être ajouté à Git. La configuration dépend de l'environnement : voir la [documentation Laravel](https://laravel.com/docs/13.x/configuration).

Avec SQLite, aucun serveur MySQL n'est nécessaire. Laisser `DB_DATABASE` et `DB_URL` commentés ou absents pour utiliser `database/database.sqlite`. Ne pas mettre `DB_DATABASE=` avec une valeur vide : cela remplace le chemin par défaut. Si une autre base est choisie, créer la base sur le serveur, activer son pilote PDO et renseigner la connexion et les identifiants adaptés au lieu de suivre la création SQLite ci-dessous.

Créer le fichier SQLite uniquement s'il est absent, pour ne jamais écraser une base existante :

```powershell
if (-not (Test-Path -LiteralPath .\database\database.sqlite)) {
    New-Item -ItemType File -Path .\database\database.sqlite | Out-Null
}
```

Vérifier les dossiers nécessaires au fonctionnement de Laravel ; cette commande crée uniquement ceux qui manquent :

```powershell
$planoraDirectories = @(
    'bootstrap/cache',
    'storage/app/private',
    'storage/app/public',
    'storage/framework/cache/data',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs'
)
foreach ($planoraDirectory in $planoraDirectories) {
    if (-not (Test-Path -LiteralPath $planoraDirectory)) {
        New-Item -ItemType Directory -Path $planoraDirectory -Force | Out-Null
    }
}
```

Le compte qui exécute PHP doit pouvoir écrire dans `storage`, `bootstrap/cache` et dans le dossier contenant la base SQLite.

## 5. Installer et préparer Laravel

Vérifier les exigences du fichier verrouillé, puis installer ses versions exactes :

```powershell
composer check-platform-reqs --lock
composer install
composer check-platform-reqs
```

Si Composer signale une extension ou une version PHP manquante, la corriger avant de continuer. Utiliser `composer install`, pas `composer update` ni `--ignore-platform-reqs`. Les scripts Composer peuvent eux-mêmes lancer `artisan package:discover`. Voir les [commandes Composer](https://getcomposer.org/doc/03-cli.md).

Effacer une éventuelle configuration en cache avant de lire le `.env` du poste :

```powershell
php artisan config:clear
```

**Seulement pour un nouvel environnement dont `APP_KEY` est vide**, générer la clé :

```powershell
php artisan key:generate
```

La clé doit rester identique lors des mises à jour et des restaurations de cet environnement. Ne pas lancer cette commande pour résoudre un autre problème.

Créer ou mettre à jour les tables :

```powershell
php artisan migrate
```

La configuration utilise la base pour les sessions, le cache et la file de tâches. Un fichier SQLite vide ne suffit donc pas : les migrations doivent réussir avant d'ouvrir le site. Éviter `optimize:clear` avant cette étape, car il peut accéder à la table de cache encore absente.

Ne pas utiliser `composer setup` comme raccourci de réinstallation : le script actuel relance notamment `key:generate`, les migrations et l'installation frontend.

### Étapes facultatives

Si les vues servent des images ou fichiers depuis le disque public Laravel :

```powershell
php artisan storage:link
```

Cette commande relie `public/storage` à `storage/app/public`. Sous Windows, la création du lien peut demander les droits appropriés ou le mode développeur.

Pour des données de démonstration, lire d'abord `database/seeders/DatabaseSeeder.php` et les seeders appelés. Exécuter `php artisan db:seed` uniquement si leur contenu convient à la branche et à la base locale. Ne pas lancer de seed automatiquement en production, ni utiliser `migrate:fresh` pour une simple mise à jour : cette dernière commande supprime les tables.

## 6. Construire les fichiers CSS et JavaScript

Laravel utilise le build Vite dans `public/build`, notamment `public/build/manifest.json`. Ce dossier est généré sur chaque poste ou lors du déploiement. **Il n'est pas nécessaire de laisser Vite tourner pour simplement utiliser le site après le build.**

Les scripts sont regroupés dans `scripts/windows` à la racine Git. Choisir une seule version, depuis le dossier Laravel actuel :

**PowerShell :**

```powershell
& ..\scripts\windows\build-frontend.ps1
```

**Batch, utilisable également depuis PowerShell :**

```powershell
& ..\scripts\windows\build-frontend.bat
```

Depuis la racine Git, les chemins sont `& .\scripts\windows\build-frontend.ps1` ou `& .\scripts\windows\build-frontend.bat`. Dans `cmd.exe`, lancer le chemin `.bat` sans le `&` initial. Si la politique PowerShell bloque le `.ps1`, utiliser le `.bat` ; aucun changement de politique global n'est nécessaire.

Ces scripts vérifient les outils, les versions et les fichiers requis, installent les dépendances frontend puis construisent les assets. Ils vérifient la présence du manifeste et s'arrêtent avec un code non nul en cas d'erreur. Ils n'exécutent ni Composer, ni Artisan, ni serveur de développement.

L'équivalent manuel, toujours depuis le dossier Laravel, est :

```powershell
npm.cmd ci --include=dev --include=optional
npm.cmd run build
```

`npm ci` réinstalle `node_modules` à partir de `package-lock.json`, sans recalculer les versions. Les dépendances de développement contiennent Vite et sont nécessaires **pour construire**, même lorsque le résultat est destiné à la production. Les dépendances optionnelles incluent des binaires adaptés à Windows. Voir [`npm ci`](https://docs.npmjs.com/cli/v11/commands/npm-ci/).

Le `.npmrc` du projet contient `ignore-scripts=true` : le conserver. Cela bloque les scripts automatiques d'installation des dépendances, mais autorise la commande explicitement demandée `npm run build`. Vite est installé localement par npm ; inutile de l'installer globalement ou d'utiliser une commande de création de projet.

Le premier lancement peut prendre du temps : téléchargement des paquets, installation et compilation. Le build actuel télécharge aussi des polices depuis `fonts.bunny.net` : une connexion réseau est nécessaire. Si `public/hot` existe, les scripts demandent d'arrêter Vite et de vérifier ce fichier avant de continuer ; voir le dépannage ci-dessous.

Après succès :

```powershell
Test-Path .\public\build\manifest.json
```

La réponse doit être `True`, et le dossier `public/build` doit aussi contenir les fichiers référencés par le manifeste. Pour le fonctionnement général, voir le [guide Vite](https://vite.dev/guide/).

## 7. Ouvrir le site

### Avec Herd

Laisser Herd ouvert et associer **le dossier Laravel qui contient `artisan`**, puis ouvrir le site :

```powershell
herd link planora
herd open
```

L'adresse attendue est `http://planora.test`, cohérente avec `APP_URL`. Si ce nom sert déjà une autre application, choisir un autre nom et adapter `APP_URL`. Vérifier la version PHP du site dans Herd. Voir les [commandes de gestion des sites](https://herd.laravel.com/docs/windows/advanced-usage/command-line).

### Avec le serveur local PHP

Alternative à Herd : mettre `APP_URL=http://127.0.0.1:8000` dans `.env`, puis :

```powershell
php artisan config:clear
php artisan serve
```

Ouvrir l'adresse indiquée dans le terminal et garder ce terminal ouvert. `Ctrl+C` arrête le serveur. Cette commande sert au développement local, pas à l'hébergement public.

## 8. Reprendre le développement et mettre à jour

Avant de récupérer du code, vérifier `git status` et sauvegarder son travail sur sa branche. Récupérer les modifications selon le fonctionnement de l'équipe. Après récupération, depuis le dossier Laravel :

```powershell
composer install
php artisan config:clear
php artisan migrate
& ..\scripts\windows\build-frontend.bat
```

Conserver `.env`, la base et `APP_KEY`. Si `.env.example` a changé, reporter uniquement les nouveaux paramètres nécessaires dans son `.env`.

Pour travailler sur les styles ou le JavaScript avec actualisation automatique, lancer **à la place du build ponctuel** :

```powershell
npm.cmd run dev
```

Garder ce terminal ouvert ainsi que Herd ou le serveur Laravel, puis consulter l'URL de l'application Laravel : Vite fournit les assets, pas les pages PHP. À la fin, arrêter Vite avec `Ctrl+C`, puis refaire le build avant de revenir au fonctionnement sans serveur Vite. Une modification de classes CSS dans une vue Blade, des dépendances frontend ou des variables `VITE_*` peut également nécessiter un nouveau build.

Pour lancer les tests du projet sur un environnement de test correctement configuré :

```powershell
php artisan test
```

Le script `composer dev` démarre plusieurs processus, dont un serveur Vite ; il n'est pas nécessaire pour utiliser le site avec le build généré.

## 9. Préparer un déploiement

Cette section décrit les étapes applicatives. Le domaine, le certificat HTTPS, le serveur web et ses droits d'accès restent à configurer sur l'hébergement choisi.

### Code, fichiers persistants et configuration

Déployer une version validée de `main`. Le serveur doit disposer du PHP et des extensions compatibles avec le lock, y compris **PHP 8.4.1 minimum en 8.4 ou 8.5**.

Conserver d'une livraison à l'autre :

- le `.env` de production et sa `APP_KEY` ;
- la base de données et ses sauvegardes ;
- les fichiers utilisateurs, notamment `storage/app/public` si utilisé.

Pour SQLite, utiliser de préférence un chemin absolu vers une base persistante hors du dossier remplacé à chaque livraison, par exemple `DB_DATABASE=C:/sites/planora-data/database.sqlite` sur Windows. Créer le dossier et le fichier uniquement pour une première installation, puis accorder les droits d'écriture nécessaires. Sauvegarder la base avant les migrations ; ne jamais la remplacer par la base du poste de développement.

Créer la configuration privée du serveur à partir de `.env.example` seulement si elle n'existe pas encore, puis adapter au minimum :

```dotenv
APP_NAME=Planora
APP_ENV=production
APP_DEBUG=false
APP_URL=https://votre-domaine.example
SESSION_SECURE_COOKIE=true
```

Configurer ensuite la base, les sessions, le cache et les services réellement utilisés. `MAIL_MAILER=log` écrit les emails dans les logs : renseigner les paramètres SMTP si des emails doivent être envoyés. Ne placer aucun secret dans une variable `VITE_*`, car ces valeurs peuvent être intégrées au code distribué au navigateur.

La racine publique du serveur web doit être **`<racine-du-depot>/Planora/public`**, avec les requêtes Laravel dirigées vers `public/index.php`. Accorder l'écriture à `storage`, `bootstrap/cache` et au dossier de la base SQLite. Ne pas exposer la racine du dépôt. Voir le [déploiement Laravel](https://laravel.com/docs/13.x/deployment).

Si un reverse proxy gère HTTPS, configurer les proxys de confiance dans `bootstrap/app.php` avec les adresses et les en-têtes de l'infrastructure réelle.

### Assets et installation PHP

Construire les assets avec l'un des scripts Windows sur la version exacte à déployer et avec les valeurs `VITE_*` destinées à la cible, puis transférer **tout le dossier `public/build`**, comprenant le manifeste et ses fichiers. Un manifeste seul ne suffit pas. Node.js est nécessaire sur la machine de build ; le serveur qui reçoit ces fichiers déjà construits n'en a pas besoin pour les servir. Ne pas transférer `node_modules`, le `.env` local ou la base locale. Ne pas déployer `public/hot` ni compter sur le serveur Vite d'un développeur.

Préparer les dossiers inscriptibles de l'étape 4 avant l'installation. Depuis le dossier Laravel du serveur, après configuration des fichiers persistants :

```powershell
composer check-platform-reqs --lock --no-dev
composer install --no-dev --prefer-dist --optimize-autoloader
composer check-platform-reqs --no-dev
php artisan config:clear
```

Sur **la toute première installation uniquement**, si `APP_KEY` est encore vide, lancer `php artisan key:generate --force`, puis conserver cette clé. Sur un déploiement existant, garder sa valeur et passer directement à la suite.

Après sauvegarde de la base, appliquer les migrations et construire les caches :

```powershell
php artisan migrate --force
php artisan config:cache
php artisan view:cache
```

Créer `php artisan storage:link` si des fichiers publics le nécessitent et si le lien n'existe pas encore. Après une modification ultérieure de `.env`, reconstruire le cache avec `php artisan config:cache`. Pour une mise à jour sur un site déjà ouvert, organiser la maintenance ou la bascule de version avec l'hébergement afin de ne pas servir un mélange d'ancien et de nouveau code.

Si l'application utilise des tâches en file d'attente, prévoir un worker `php artisan queue:work` supervisé par l'hébergement et le redémarrer après la livraison avec `php artisan queue:restart`. De même, configurer le planificateur seulement si le projet définit des tâches planifiées. Vérifier la page d'accueil, le chargement des assets et les parcours utilisés après déploiement ; consulter `storage/logs/laravel.log` si une erreur survient.

## 10. Dépannage

| Problème | Vérification / correction |
| --- | --- |
| `Could not open input file: artisan` | Se placer dans le sous-dossier Laravel. `Test-Path .\artisan` doit retourner `True`. |
| `Vite manifest not found` | Exécuter un script frontend et vérifier sa réussite, puis `public/build/manifest.json`. Le manifeste est créé par le build, pas par Composer. |
| `node`, `npm`, `php` ou `composer` introuvable | Installer/sélectionner l'outil dans Herd, rouvrir le terminal et vérifier sa version. `Get-Command php`, `Get-Command node` et `Get-Command npm.cmd` montrent le programme utilisé. |
| Version PHP incompatible | Sélectionner PHP 8.4 ou 8.5 à jour pour le terminal et le site ; relancer `composer check-platform-reqs --lock`. |
| `npm.ps1 cannot be loaded` | Utiliser `npm.cmd` ou le script `.bat`. |
| Script `.ps1` bloqué | Utiliser la version `.bat` depuis un terminal ; elle ne dépend pas de la politique des scripts PowerShell. |
| `EBADENGINE` / Node incompatible | Sélectionner Node.js 24 LTS ; les scripts attendent au minimum 22.13.0. Rouvrir le terminal. |
| Module natif Rolldown / Tailwind / Oxide absent | Arrêter les processus qui utilisent `node_modules`, vérifier la version Node, puis relancer `npm.cmd ci --include=dev --include=optional`. Conserver le lock et les dépendances optionnelles. |
| `npm ci` signale un désaccord avec le lock | Faire corriger et valider ensemble `package.json` et `package-lock.json` sur la branche concernée. Ne pas supprimer le lock pour contourner l'erreur. |
| Build bloqué sur une police ou un téléchargement | Vérifier l'accès réseau, le proxy et l'accès à Bunny Fonts ou au registre npm mentionné dans l'erreur. |
| `MissingAppKeyException` | Vérifier `.env`, lancer `config:clear`, et générer une clé seulement si l'environnement n'en possède pas encore. |
| Fichier SQLite absent | Vérifier `DB_CONNECTION`, le chemin éventuel `DB_DATABASE`, créer le fichier uniquement s'il manque puis lancer `migrate`. |
| `no such table: sessions` ou `cache` | Lancer les migrations sur la base effectivement configurée. |
| `tempnam()` / dossier de vues introuvable | Vérifier `storage/framework/views`, les dossiers de l'étape 4 et leurs droits d'écriture ; lancer `php artisan config:clear` puis `php artisan view:clear`. |
| HTTP 500 | En local, regarder l'erreur précise et `Get-Content .\storage\logs\laravel.log -Tail 80`. En production, conserver `APP_DEBUG=false`. |
| CSS absent malgré un build réussi | Vérifier `public/hot`, puis les fichiers `public/build`, l'URL et les erreurs réseau du navigateur. |

### Un ancien fichier `public/hot` est présent

Ce fichier indique à Laravel d'utiliser le serveur Vite. **Arrêter d'abord tous les `npm run dev` du projet**. Si le serveur Vite est bien arrêté et que le fichier est resté après une interruption, le supprimer depuis le dossier Laravel :

```powershell
if (Test-Path -LiteralPath .\public\hot) {
    Remove-Item -LiteralPath .\public\hot
}
```

Relancer ensuite le script de build. Ne pas supprimer ce fichier pendant qu'un serveur Vite est encore utilisé.
