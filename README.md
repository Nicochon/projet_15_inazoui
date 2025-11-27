# Ina Zaoui
photographe spécialisée dans les photos de paysages du monde entier.
Ce projet est une application web permettant de gérer et présenter les photos de manière organisée.

## Pré-requis
Avant de commencer, assurez-vous d’avoir installé sur votre machine :
- PHP >= 8.1
- Composer
- Symfony CLI
- MySQL / MariaDB
- Docker & Docker Compose (optionnel, si utilisé)

## Installation
### Clonez le dépôt :
```bash 
git clone https://github.com/Nicochon/projet_15_inazoui.git 
```
cd projet_15_inazoui
### Installez les dépendances PHP :
```bash 
composer install 
```
### Créez la base de données et appliquez les migrations :
```bash 
php bin/console doctrine:database:create 
php bin/console doctrine:migrations:migrate 
```
Vous pouvez importer votre base de données grace au fichier suivant:
- album.sql
- media.sql
- user.sql
### Lancez le serveur Symfony :
```bash 
symfony serve 
```

## Usage:
### Accès à administrateur
**Administrateur** | admin@inazoui.com | password 
### Routes importantes. 
/admin/album: Page de gestion des album dans l'admin
/admin/guests: Page de gestion des utilisateurs dans l'admin
/admin/media: Page de gestion des medias dans l'admin
/login: page de connexion
/: Page d'accueil publique.
#### Affichez toutes les routes du projet:
```bash 
php bin/console  debug:router 
```


## Utilisation des tests
Des test ont été implémantés dans le projet, ils couvrent actuellement 72% des lignes de ce projet.
### Générer le rapport de couverture 
```bash 
vendor/bin/phpunit --coverage-html public/test-coverage
```

## phpStan
commande pour lancer phpStan:
```bash 
vendor/bin/phpstan analyse src
```

## php fixer
lancer un controle:
vendor/bin/php-cs-fixer fix --dry-run --diff

corriger
vendor/bin/php-cs-fixer fix

### Lancer tous les tests
```bash 
vendor/bin/phpunit 
```
### Lancer un test
```bash 
vendor/bin/phpunit tests/DossierDeTest/fichierDeTest.php
```

## Amélioration
### Accéder à l'admin:
Pas de page home dans l'admin. il faut y accéder directement dans l'url.
### Création de compte
Les utilisateurs ne peuvent pas créer de compte.
### Possibilité à l'admin de modifier un utilisateur. 
Actuellement l'utilisateur admin ne peut pas modifier un utilisateur. 



Vous trouverez dans le fichier `backup.zip` un dump SQL anonymisé de la base de données et toutes les images qui se trouvaient dans le dossier `public/uploads`.
Faudrait peut être trouver une meilleure solution car le fichier est très gros, il fait plus de 1Go.