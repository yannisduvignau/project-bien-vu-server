# Laravel Server BienVu App

## Description du projet
Ce projet est une API RESTful construite avec Laravel, un framework PHP puissant et flexible. Il est conçu pour fournir une architecture robuste et évolutive, intégrant la documentation API avec Swagger L5.

## Installation

### Prérequis
Avant d'installer le projet, assurez-vous d'avoir les éléments suivants installés sur votre machine :
- PHP (>= 8.0)
- Composer

### Étapes d'installation
1. **Cloner le dépôt**
   ```bash
   git clone https://github.com/Ecole-de-Turing/BienVu-back.git
   cd BienVu-back
   ```

2. **Installer les dépendances PHP**
   ```bash
   composer install
   ```

3. **Configurer l'environnement**
   Copiez le fichier d'exemple de configuration et modifiez-le si nécessaire :
   ```bash
   cp .env.example .env
   ```
   Mettez à jour les variables d'environnement dans le fichier `.env`, notamment les configurations de la base de données.

4. **Générer la clé d'application**
   ```bash
   php artisan key:generate
   ```

5. **Lancer le serveur**
   ```bash
   php artisan serve
   ```
   L'API sera accessible sur `http://127.0.0.1:8000`

## Documentation API (Swagger L5)

### Installation de Swagger L5
Si Swagger n'est pas encore installé, ajoutez-le via composer :
```bash
composer require darkaonline/l5-swagger
```

Puis, publiez la configuration et générez la documentation :
```bash
php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"
php artisan l5-swagger:generate
```

### Accéder à la documentation
Une fois le serveur Laravel en cours d'exécution, accédez à la documentation Swagger via :
```
http://127.0.0.1:8000/api/documentation
```

## Structure du projet
```
├── app/                # Contient les fichiers principaux de l'application
├── bootstrap/          # Fichiers de démarrage de Laravel
├── config/             # Fichiers de configuration
├── database/           # Migrations et seeders
├── public/             # Point d'entrée du projet
├── resources/          # Vues, langages, assets
├── routes/             # Fichiers de routes
├── storage/            # Logs, cache, et uploads
├── tests/              # Tests unitaires et fonctionnels
└── vendor/             # Dépendances gérées par Composer
```

## Contributeurs

- **Paul Banchon** - [GitHub](https://github.com/P0B0CK)
- **Yannis Duvignau** - [GitHub](https://github.com/yannisduvignau)