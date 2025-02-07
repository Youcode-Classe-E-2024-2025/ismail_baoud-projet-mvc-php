# Projet MVC PHP avec PostgreSQL

## Description
Ce projet vise à concevoir une architecture MVC propre et modulaire en PHP, avec PostgreSQL comme base de données. L'objectif est d'assurer une séparation stricte des responsabilités entre le modèle, la vue et le contrôleur, tout en garantissant la sécurité, l'extensibilité et la maintenabilité du code.

## Fonctionnalités principales
- Gestion avancée des routes avec un routeur personnalisé
- Connexion sécurisée à PostgreSQL via PDO
- Séparation du Front Office et du Back Office
- Système d’authentification sécurisé (sessions, tokens, permissions)
- Gestion des rôles et autorisations (ACL)
- Moteur de templates Twig pour les vues
- Injection de dépendances et gestion des services
- Sécurisation des requêtes SQL contre les injections SQL et XSS
- Système de logs et gestion des erreurs
- Utilisation de Design Patterns (Repository Pattern, Service Container)
- Classe Validator pour la validation des données
- Classe Security pour la protection CSRF, XSS et SQL Injection
- Classe Session pour la gestion avancée des sessions
- Utilisation d’un fichier .htaccess pour la réécriture des URL et la sécurité

## Structure du projet
```
/projet-mvc-php
│── public/
│   ├── index.php
│   ├── .htaccess
│   ├── assets/
│
│── app/
│   ├── controllers/
│   │   ├── front/
│   │   │   ├── HomeController.php
│   │   │   ├── ArticleController.php
│   │   ├── back/
│   │   │   ├── DashboardController.php
│   │   │   ├── UserController.php
│   ├── models/
│   │   ├── User.php
│   │   ├── Article.php
│   ├── views/
│   │   ├── front/
│   │   │   ├── home.twig
│   │   │   ├── article.twig
│   │   ├── back/
│   │   │   ├── dashboard.twig
│   │   │   ├── users.twig
│   ├── core/
│   │   ├── Router.php
│   │   ├── Controller.php
│   │   ├── Model.php
│   │   ├── View.php
│   │   ├── Database.php
│   │   ├── Auth.php
│   │   ├── Validator.php
│   │   ├── Security.php
│   │   ├── Session.php
│   ├── config/
│   │   ├── config.php
│   │   ├── routes.php
│── logs/
│── vendor/
│── .env
│── composer.json
│── .gitignore
```

## Installation
### Prérequis
- PHP >= 8.0
- PostgreSQL
- Composer
- Un serveur web (Apache/Nginx)

### Étapes d'installation
1. Cloner le dépôt
   ```sh
   git clone https://github.com/ton-utilisateur/projet-mvc-php.git
   cd projet-mvc-php
   ```
2. Installer les dépendances avec Composer
   ```sh
   composer install
   ```
3. Configurer l’environnement
   - Copier le fichier `.env.example` en `.env`
   - Modifier les variables de connexion à la base de données

4. Importer la base de données
   ```sh
   psql -U ton-utilisateur -d ta-base < database/schema.sql
   ```

5. Lancer le serveur PHP intégré
   ```sh
   php -S localhost:8000 -t public
   ```

## Utilisation
- Accéder au Front Office : `http://localhost:8000`
- Accéder au Back Office : `http://localhost:8000/admin`

## Bonnes Pratiques
### Sécurité
- Protection CSRF avec tokens
- Validation des entrées utilisateur
- Protection XSS et SQL Injection

### Architecture
- Séparation stricte des responsabilités
- Modularité et extensibilité
- Utilisation de Twig pour séparer la logique de l’affichage

### Optimisation
- Optimisation des requêtes SQL
- Respect des conventions de nommage PSR-1 et PSR-12
- Documentation du code

## Contribution
Les contributions sont les bienvenues ! Merci de créer une pull request pour proposer des améliorations.

## Auteur
Ismail Baoud

## Licence
Ce projet est sous licence MIT - voir le fichier [LICENSE](LICENSE) pour plus de détails.

