# Zeno

Zeno est une application web de quiz quotidien : chaque jour, le joueur répond à
une question de culture générale et fait grandir (ou bouder) un compagnon
virtuel, un chat dont l'humeur évolue avec la série de bonnes réponses.

Ce README explique les choix techniques du projet et donne toutes les étapes
pour le cloner et le reprendre en main sur une nouvelle machine.

## Stack technique

| Brique | Choix | Pourquoi |
|---|---|---|
| Framework | Symfony 8.1 (PHP >= 8.4) | Framework MVC complet, conventions fortes, écosystème de composants (Form, Validator, Security, Serializer...) plutôt que de tout réécrire à la main. |
| Persistance | Doctrine ORM 3.x + migrations | Mapping objet-relationnel avec attributs PHP, et évolution du schéma tracée via des migrations versionnées plutôt que des `ALTER TABLE` manuels. |
| Base de données | MySQL 8.0 | Choix par cohérence d'écosystème : c'est la base utilisée par des outils PHP/Symfony proches de ce type de projet (par ex. teemIP, l'add-on de gestion IP pour GLPI, tourne sur MySQL/MariaDB). Rester sur MySQL évite d'avoir deux SGBD différents à maintenir/héberger si le projet est un jour déployé à côté d'un outil de ce type. Le projet n'utilise aucune fonctionnalité propriétaire MySQL : passer à MariaDB ou PostgreSQL ne demanderait que de changer `DATABASE_URL`. |
| Rendu des pages | Twig + composants Twig (`symfony/ux-twig-component`) | Rendu côté serveur, avec des composants réutilisables (`templates/components/ui/*`, `templates/components/sections/*`) plutôt que des templates dupliqués. |
| Interactions front | Stimulus (`symfony/stimulus-bundle`) + Turbo (`symfony/ux-turbo`) | Ajoute de l'interactivité (dropdown, modal de quiz, indicateur de force du mot de passe...) sans construire une SPA séparée : cohérent avec un rendu Twig côté serveur. |
| Gestion des assets | AssetMapper (`symfony/asset-mapper`) | Pas de bundler Node (Webpack/Vite) à maintenir pour un projet de cette taille ; les fichiers JS sous `assets/` sont servis tels quels via l'import map. |
| CSS | Tailwind CSS (`symfonycasts/tailwind-bundle`) | Utilitaire, cohérent avec l'absence de build Node : le bundle compile le CSS via un binaire, sans `npm install`. |
| Auth | `symfony/security-bundle`, authenticator par formulaire | Inscription/connexion classiques par email + mot de passe, avec hashage du mot de passe et consentement RGPD stocké à l'inscription. |
| Mails (dev) | `symfony/mailer` + Mailpit (Docker) | Capture les emails envoyés en local sans SMTP réel. |
| Tests | PHPUnit (`symfony/test-pack`) | `WebTestCase` pour les tests fonctionnels HTTP, `KernelTestCase` pour les tests de service. |

Le choix général a été de rester sur ce que Symfony propose nativement
(Form, Validator, Security, Doctrine) plutôt que d'ajouter des dépendances
tierces, conformément aux conventions du projet (voir [AGENTS.md](AGENTS.md)).

## Fonctionnement du jeu

- Une question par jour, la même pour tous les joueurs, calculée à partir de
  minuit **Europe/Paris** (`src/Quiz/QuizClock.php`) — indépendant du fuseau
  horaire du joueur.
- Une bonne réponse incrémente la série (streak) et le score total ; une
  mauvaise réponse peut être retentée tant que le jour n'est pas validé.
- Un jour sans réponse casse la série et fait "bouder" le chat : la logique de
  rattrapage des jours manqués vit dans
  `src/Quiz/QuizProgressService::catchUpMissedDays()`.
- L'humeur du chat (`src/Quiz/CatMood.php`) dépend de paliers de bonnes
  réponses consécutives (colère → triste → neutre → satisfait).

Entités principales : `User`, `Question` / `QuestionChoice`, `DailyAnswer`
(réponse d'un utilisateur à une date donnée), `UserQuizProgress` (état du
streak/score par utilisateur).

## Prérequis

- PHP >= 8.4 avec les extensions `ctype` et `iconv`
- [Composer](https://getcomposer.org/)
- [Symfony CLI](https://symfony.com/download) (recommandé pour `symfony serve`
  et `symfony console`)
- Docker (pour MySQL, Adminer et Mailpit via `compose.yaml`) — ou un serveur
  MySQL 8.0 local si vous ne voulez pas utiliser Docker

## Installation

```bash
git clone <url-du-repo> zeno
cd zeno

# Dépendances PHP
composer install

# APP_SECRET (voir section Configuration ci-dessous) : .env ne le fournit pas
echo "APP_SECRET=$(php -r 'echo bin2hex(random_bytes(16));')" > .env.dev

# Démarre MySQL (+ Adminer + Mailpit) en arrière-plan
docker compose up -d

# Base de données : création du schéma + données de démo
symfony console doctrine:migrations:migrate --no-interaction
symfony console doctrine:fixtures:load --no-interaction

# Démarre le serveur Symfony (avec TLS local et watcher Tailwind, cf. .symfony.local.yaml)
symfony serve -d
```

L'application est alors disponible via `symfony open:local`.

Sans Symfony CLI, remplacez `symfony console` par `php bin/console` et
`symfony serve -d` par votre propre serveur PHP (l'intégration FrankenPHP en
mode dev/hot-reload de `templates/base.html.twig` est optionnelle et ne
s'active que si la variable d'environnement correspondante est présente).

## Configuration

- `.env` est committé et ne contient que des valeurs par défaut de dev
  (`APP_SECRET` y est volontairement vide). Ne jamais y mettre de vrai secret.
- `.env.dev` est **ignoré par git** (voir `.gitignore`) : c'est là qu'`APP_SECRET`
  est réellement défini pour l'environnement `dev`. Après un clone, ce fichier
  n'existe pas encore : créez-le (ou un `.env.local`) avec au minimum

  ```bash
  APP_SECRET=$(php -r 'echo bin2hex(random_bytes(16));')
  ```

  sinon Symfony démarre avec un `APP_SECRET` vide.
- Les autres overrides locaux (mots de passe réels, clés d'API...) vont dans
  `.env.local` (ignoré par git) ou dans le coffre à secrets Symfony
  (`bin/console secrets:set`).
- `DATABASE_URL` par défaut pointe vers le MySQL du `compose.yaml`
  (`mysql://app:!ChangeMe!@127.0.0.1:3306/app`). Changez le mot de passe en
  production.
- `compose.override.yaml` ajoute Adminer (`http://localhost:8080`, serveur
  `database`) pour inspecter la base en dev, et Mailpit (interface web sur le
  port exposé dynamiquement, cf. `docker compose ps`) pour lire les emails
  envoyés en local.

## Commandes utiles

```bash
# Tests
php bin/phpunit

# Style de code (si friendsofphp/php-cs-fixer est installé)
vendor/bin/php-cs-fixer fix

# Nouvelle migration après modification d'une entité
symfony console make:migration
symfony console doctrine:migrations:migrate

# Explorer les routes, services, config
symfony console debug:router
symfony console debug:container
symfony console debug:config <bundle>

# Profiler web (en dev)
# http://localhost/_profiler
```

## Structure du projet

```
src/
  Controller/     Contrôleurs HTTP (fins, délèguent aux services)
  Entity/         Entités Doctrine
  Repository/     Requêtes Doctrine spécifiques
  Form/           Formulaires Symfony (inscription, réponse au quiz)
  Quiz/           Logique métier du jeu (horloge, humeur, service de progression)
  Faq/            Contenu de la FAQ affichée sur la page d'accueil
  DataFixtures/   Données de démonstration (utilisateurs, questions)
templates/
  components/     Composants Twig réutilisables (ui/, layout/, sections/)
  <feature>/      Templates propres à chaque fonctionnalité (learn, registration, security, legal)
assets/           JS (contrôleurs Stimulus) et CSS (Tailwind), servis via AssetMapper
migrations/       Historique du schéma de base de données
```

## Ajouter une fonctionnalité

Ce projet suit les conventions décrites dans [AGENTS.md](AGENTS.md) : usage
systématique des attributs PHP (`#[Route]`, `#[MapRequestPayload]`,
`#[IsGranted]`...), autowiring plutôt que configuration YAML manuelle, et
installation des nouvelles briques via `composer require <package>` (Flex se
charge d'enregistrer le bundle et de générer sa config) plutôt qu'en éditant
`config/bundles.php` à la main.
