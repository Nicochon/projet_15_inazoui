# Contribuer au projet Ina Zaoui

Process our signaler des problèmes, proposer de nouvelles fonctionnalités, améliorer le code, les tests ou la documentation.

---

## Table des matières
1. [Signaler un problème](#signaler-un-problème)
2. [Proposer une fonctionnalité](#proposer-une-fonctionnalité)
3. [Contribuer au code](#contribuer-au-code)
4. [Contribuer aux tests](#contribuer-aux-tests)
5. [Contribuer à la documentation](#contribuer-à-la-documentation)
6. [Bonnes pratiques](#bonnes-pratiques)

---

## Signaler un problème

Si vous rencontrez un bug ou un comportement inattendu:

1. Vérifier si le problème a déjà été signalé dans la section [Issues](https://github.com/Nicochon/projet_15_inazoui/issues) du dépôt.
2. Si ce n’est pas le cas, créer une nouvelle issue avec :
    - Un titre clair et descriptif.
    - Une description détaillée du problème.
    - Les étapes pour reproduire le bug.
    - Les informations sur votre environnement (PHP, Symfony, base de données, navigateur…).
    - Les messages d’erreur éventuels.

---

## Proposer une fonctionnalité

Pour suggérer une amélioration ou une nouvelle fonctionnalité :

1. Vérifiez qu’une proposition similaire n’existe pas déjà dans les [Issues](https://github.com/Nicochon/projet_15_inazoui/issues).
2. Créez une nouvelle issue en précisant :
    - L’objectif de la fonctionnalité.
    - L’impact attendu.
    - Les éventuelles captures d’écran ou maquettes.

---

## Contribuer au code

Pour contribuer au code source du projet :

1. Cloner le dépôt.
2. Créez une nouvelle branche pour votre fonctionnalité ou correction
3. Installez les dépendances et configurez la base de données comme indiqué dans le README
4. Apportez vos modifications et testez-les.
5. Commitez vos changements
6. Poussez votre branche
7. Créez une Pull Request vers la branche principale (main)

---

## Contribuer aux tests

1. Les tests sont situés dans le dossier tests/.
2. Pour lancer tous les tests :
```bash 
vendor/bin/phpunit 
```
3. Lancer un test
```bash 
vendor/bin/phpunit tests/DossierDeTest/fichierDeTest.php
```
4. Pour générer le rapport de couverture :
```bash 
vendor/bin/phpunit --coverage-html public/test-coverage
```
### N’oubliez pas d’écrire de nouveaux tests pour toute fonctionnalité ou correction que vous apportez.

---

## Contribuer à la documentation

### La documentation est dans le README.md et ce fichier CONTRIBUTING.md.
### Pour corriger une faute, ajouter des détails ou améliorer la clarté :
1. Créez une branche. 
2. Modifiez le fichier concerné. 
3. Soumettez une Pull Request.

---

