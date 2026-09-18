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

## Pochettes des albums

Importer une seule fois `BDD/003_album_cover.sql` après la migration 002 (déjà appliquée sur la base locale lors du développement). PHP doit disposer de GD et fileinfo ; le dossier `uploads/albums` doit être accessible en écriture au serveur PHP. Configurer `upload_max_filesize=5M` et `post_max_size=8M` ou plus pour autoriser la taille annoncée.

Dans **Admin → Albums → Créer / Modifier**, choisir une pochette JPEG, PNG ou WebP (5 Mo maximum, 6 000 pixels par côté et 16 mégapixels). Le fichier est contrôlé puis réencodé et enregistré sous un nom aléatoire. Sans fichier, la pochette existante reste inchangée ; la case « Supprimer la pochette actuelle » permet de la retirer. Une nouvelle image prend priorité sur cette case. Les anciennes images sont supprimées après validation de la modification ou suppression de l’album (y compris par cascade depuis artiste/groupe).

L’API accepte le fichier `imageA` en `multipart/form-data` et `remove_image=1` pour le retrait. Le chemin `imageA` est retourné en lecture, mais ne peut pas être imposé par un champ texte. Les tests d’intégration vérifient aussi l’envoi multipart, le rejet d’un faux fichier, le remplacement, l’affichage et la suppression de la pochette.

## Après un pull / installation d’un autre poste

Le pull ne met pas à jour MySQL. Après configuration du `.env` local et import du schéma initial si nécessaire, lancer `php scripts/migrate.php`. Cette commande ajoute les colonnes d’authentification et de pochette manquantes et peut être relancée. Ne pas réimporter le schéma initial sur une base existante. Chaque poste doit utiliser ses propres paramètres MySQL et son propre `BASE_URL`.

L’inscription distingue désormais une adresse déjà utilisée, un schéma incomplet et une base indisponible. Les mots de passe exigent 12 caractères, une majuscule ASCII, une minuscule ASCII, un chiffre et un caractère spécial, dans la limite de 72 octets ; la validation serveur reste obligatoire même sans JavaScript.

Les pages légales mentionnent Mehdi Afankous, son contact et Hostinger. Avant publication, compléter les coordonnées légales de l’éditeur, l’entité Hostinger contractuelle, les durées des journaux/sauvegardes et les conditions de transfert des fournisseurs. Le bandeau conserve acceptation/refus pendant 180 jours ; aucun traceur facultatif n’est actuellement activé. Toute intégration future doit être explicitement décrite et conditionnée à un nouveau consentement.

## Référencement, liens et antispam

Les titres, descriptions, URL canoniques et balises Open Graph/Twitter sont générés dans le header. Le partage d’un album reprend sa pochette ; Michel est utilisé par défaut. Les textes alternatifs décrivent les images utiles ; les images purement décoratives peuvent conserver une alternative vide.

Définir `BASE_URL` avec le domaine public HTTPS puis lancer `php scripts/generate-seo.php` pour générer `robots.txt` et l’instantané `sitemap.xml`. Le fichier robots référence `/sitemap.php`, qui lit toujours les albums actuels et reste à jour après les CRUD. Les pages privées et de recherche ne sont pas indexables. Le sitemap local ne doit pas être envoyé à un moteur de recherche avant configuration du domaine public.

`404.php` répond avec un vrai statut 404 sous Apache et le serveur PHP de développement. Pour une installation Apache en sous-dossier, adapter `ErrorDocument 404 /404.php` dans `.htaccess` au chemin de ce sous-dossier.

Exécuter `php scripts/migrate.php` après le pull pour créer la table de limitation antispam. Les inscriptions utilisent un champ piège et une limite de 5 tentatives par adresse IP sur une fenêtre de 15 minutes ; les connexions sont limitées à 20 tentatives. Les compteurs expirés sont purgés lors des nouvelles tentatives. Les en-têtes de proxy fournis par le client ne sont pas utilisés. Les formulaires conservent la validation serveur et les jetons CSRF ; les contrôles JavaScript complètent la validation native du navigateur.

Vérifications réalisées : tests HTTP/PHP des CRUD, du partage, du sitemap, de la 404, du piège antispam et du code 429 ; contrôle des liens publics internes. Les styles contiennent des adaptations mobile/tablette, mais une vérification visuelle sur appareils réels reste à effectuer.
