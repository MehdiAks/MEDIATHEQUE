# Médiathèque musicale

Application PHP 8.1+ / MySQL : catalogue, recherche, détail des albums, pistes, favoris, inscription, connexion et administration.

## Installation

1. Configurer `.env` à partir de `.env.example` : `DB_HOST`, `DB_PORT` (8889 pour certaines installations MAMP), `DB_USER`, `DB_PASSWORD`, `DB_DATABASE`, `BASE_URL`. `DB_SOCKET` est disponible pour une connexion par socket. Activer les extensions PDO MySQL et mbstring.
2. Pour une nouvelle base, importer `BDD/CreateDbMediatheq22.sql` puis `BDD/002_authentication.sql`. Pour une base existante, importer **uniquement la migration 002, une seule fois**. Sélectionner la bonne base avant l’import. Le nom du schéma initial est `MEDIATHEQ22` ; adapter le script si un autre nom est nécessaire.
3. Créer l’administrateur avec `php scripts/create-admin.php votre@email.fr`, puis saisir son mot de passe sur l’entrée standard. La commande permet aussi de promouvoir un compte existant et de remplacer son mot de passe. Aucun compte public ne reçoit automatiquement les droits administrateur.
4. Servir le dossier du projet avec MAMP, ou lancer `php -S localhost:8000 router.php` et définir `BASE_URL=http://localhost:8000`.
   Sous Apache, activer les fichiers `.htaccess` (`AllowOverride All`, sans dépendance à `mod_rewrite`) pour protéger les fichiers de configuration et les scripts internes.
5. Se connecter puis ouvrir **Admin**. Créer les groupes/artistes, les albums, puis les titres.

Sur ce Mac, PHP est disponible à `/Applications/MAMP/bin/php/php8.3.28/bin/php`.

La migration ajoute les mots de passe hachés et les droits administrateur à `USER`, sans supprimer les données. Elle autorise les artistes solo et les albums liés à un artiste ou à un groupe. Les comptes existants sans mot de passe peuvent recevoir un mot de passe depuis l’administration. Les durées des titres sont exprimées en secondes. Les suppressions suivent les cascades du schéma SQL ; une confirmation est demandée dans l’interface.

## API

Ressources : `groupes`, `artistes`, `albums`, `titres`, `users`, `likes`.

- `GET /api/{ressource}/read.php` : liste ; ajouter la clé en query string pour un élément.
- `POST /api/{ressource}/create.php` : création.
- `POST /api/{ressource}/update.php` : modification, avec les champs et les clés initiales préfixées `original_`.
- `POST /api/{ressource}/delete.php` : suppression, avec les clés.

Clés : `idGp`, `idArt`, `idAlb`, `idTit`, `eMailUser` ; pour `likes`, la paire `eMailUser` + `idAlb`. Les champs métier portent les noms du schéma SQL. Les champs et relations sont centralisés dans `functions/data.php`. Les utilisateurs acceptent aussi un champ `password` facultatif ; les mots de passe et les droits ne sont jamais exposés par les lectures API.

Ces endpoints sont réservés aux administrateurs connectés. Envoyer les cookies de session et un jeton `csrf_token` issu d’un formulaire, ou l’en-tête `X-CSRF-Token`, pour les mutations. Les corps JSON sont acceptés. Avec `Accept: application/json`, la réponse est `{ "success": true, "data": ... }` ou `{ "success": false, "error": ... }`. Codes : 201 création, 200 succès, 401 connexion requise, 403 accès/CSRF refusé, 404 absent, 405 méthode, 409 conflit SQL, 422 validation.

L’inscription, la connexion et la déconnexion utilisent les formulaires dans `views/backend/security` et les POST de `api/security`. Un utilisateur connecté gère ses propres favoris sur `album.php?id=...`. L’administration peut gérer tous les favoris. Les anciens exemples `statutsCC` du modèle Blogart sont retirés du parcours : aucune table STATUT n’existe dans ce schéma.

## Vérification

`php tests/integration.php` crée une base temporaire `mediatheque_test_*`, importe le schéma et la migration, lance un serveur local sur le port 18976, vérifie les CRUD, les écrans, les permissions, les validations, les favoris et la recherche, puis supprime cette base. Le compte MySQL de test doit pouvoir créer et supprimer des bases. La base configurée n’est pas modifiée par ces tests.
