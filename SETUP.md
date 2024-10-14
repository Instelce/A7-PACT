# Setup

Installer le driver `php-pgsql`. Sur ubuntu : `sudo apt-get install php-pgsql`. Sur window, il n'y a apparament pas besoin de l'installer.

Editer le fichier `php.ini`. Pour le trouver lancer `php --ini`. Puis décommentez la ligne `extension=pdo_pgsql` ie enlever le `;`.

Lancer un `composer install`.

Copier le fichier `.env.example` et le renommer en `.env`.

## Lancer le serveur de développement

Rendez-vous dans le dossier `public` et lancez la commande `php -S localhost:8080`.

## Lancer la DB de développement

Lancer la commande `docker-compose up` à la racine.
