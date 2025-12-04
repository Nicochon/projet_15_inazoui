# Ina Zaoui
photographe spécialisée dans les photos de paysages du monde entier.
Ce projet est une application web permettant de gérer et présenter les photos de manière organisée.

---

## Pré-requis
Avant de commencer, assurez-vous d’avoir installé sur votre machine :
- PHP >= 8.1
- Composer
- Symfony CLI
- MySQL / MariaDB
- Docker & Docker Compose (optionnel, si utilisé)

---

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

---

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
### Fonctionnalités principales
- Albums photo: Création, édition et affichage d'album
- Invités: Possibilité d'ajouter des guest qui peuvent uploader leurs photos
- Gestion des médias: upload, stockage et affichage des images.
- Authentification: Espace sécurisé pour l'utilisateur.
- Interface intuitive: Design épuré et responsive.
- Administration: Gestion des utilisateurs, albums et contributions.

---

## Utilisation des tests
Des test ont été implémantés dans le projet, ils couvrent actuellement 72% des lignes de ce projet.
### Générer le rapport de couverture 
```bash 
vendor/bin/phpunit --coverage-html public/test-coverage
```
### Lancer tous les tests
```bash 
vendor/bin/phpunit 
```
### Lancer un test
```bash 
vendor/bin/phpunit tests/DossierDeTest/fichierDeTest.php
```

---

## PhpStan
### Annalyse:
```bash 
vendor/bin/phpstan analyse src
```

---

## Php fixer
### Controle:
```bash 
vendor/bin/php-cs-fixer fix --dry-run --diff
```
### Correction
```bash 
vendor/bin/php-cs-fixer fix
```

---

## structure du projet 

876-p15-inazaoui/
│
├── .github/               
├── config/                
├── migrations/            
├── public/
    ├── images/
    ├── test-coverage/
    ├── uploads/
├── src/     
    ├── Controller/
        ├── Admin/
    ├── DataFixture/
    ├── Entity/
    ├── Form/
    ├── Repository/
    ├── security/
    ├── service/
├── templates/            
    ├── admin/            
    ├── front/          
├── tests/
    ├── Functional/
    ├── Service/
├── var/

---

## Contribuer 
Les conditions sont les bienvenues !
Merci de lire [CONTRIBUTING.md](CONTRIBUTING.md) pour connaitre le workflow de contribution, les bonnes pratiques Git et les normes de code a suivre.
