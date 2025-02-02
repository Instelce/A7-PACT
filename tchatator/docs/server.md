# Server

Documentation technique du server.

## Configurer

Pour configurer le server, éditez le fichier `config`.

Il faut aussi copiez le fichier `.env.example` en `.env` et l'éditez avec les informations de votre DB.

## Compiler

Pour compiler lancez simplement la commander `make server`.

## Exécuter

Avant d'exécuter le server, assurez-vous que la DB tourne.

Pour exécuter le server lancez : `./bin/server`.

### Paramètres

Le server accepte les paramètres suivants :

- `-h` : Affiche l'aide.
- `-v` : Affiche les logs du server.
- `-c <chemin>` : Chemin du fichier de configuration.

## Dépot github

[Lien du dépot](https://github.com/Instelce/A7-PACT/tree/main/tchatator)

### DB

Cloner le repo [A7-PACT](https://github.com/Victouu/A7-PACT) et rentrer dans le dossier.

Lancer la DB : `docker compose up -d`.

Lancer les migration : `php manage.php migration apply`.

Puis peuplez la DB : `php seeder/real.php`.
