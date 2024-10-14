# Setup

## A ne faire qu'une fois

Installer le driver `php-pgsql`. Sur ubuntu : `sudo apt-get install php-pgsql`. Sur window, il n'y a apparament pas besoin de l'installer.

Ensuite, **editez** le fichier `php.ini`. Pour le trouver lancer `php --ini`. Puis **décommentez** la ligne `extension=pdo_pgsql` ie enlever le `;`.

Lancer un `composer install` pour installer les dépendances.

Enfin, **copier** le fichier `.env.example` et le **renommer** en `.env`.

## Commencer à développer

Tous d'abord, lancer la DB de développement avec cette commande, a lancer à la racine du projet.

```
docker-compose up`
```

Puis lancer le serveur PHP. Rendez-vous dans le dossier `public` et lancez la commande.

```
php -S localhost:8080
```
