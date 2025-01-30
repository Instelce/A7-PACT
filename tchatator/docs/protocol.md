# Protocole

Ce fichier définit le protocole de communication de tchatator, qui est utilisé pour le chat de TripEnArvor.

Le protocole est basé sur une architecture client-serveur où le client envoie des commandes au serveur pour effectuer des actions comme l'envoi de messages, la connexion, la déconnexion, etc. Le serveur répond avec un code de statut et des données supplémentaires si nécessaire.

---

### Les commandes et leurs paramètres

Le client envoie des commandes au serveur pour réaliser diverses actions. Les commandes sont envoyées sous forme de [chaînes formatées](#format-des-commandes) et incluent des paramètres comme le jeton d'authentification, l'identifiant du destinataire, et le contenu du message.

Voici les commandes disponibles :

- `LOGIN` : Authentifie un utilisateur avec un jeton API.
    - `api-token` : La clé d'API de l'utilisateur.
- `SEND_MSG` : Envoie un message à un utilisateur.
    - `token` : Le jeton d'authentification de l'utilisateur.
    - `recipient-id` : L'identifiant de l'utilisateur destinataire.
    - `message-length` : La longueur du message.
    - `content` : Le contenu du message.
- `UPDT_MSG` : Met à jour un message existant.
    - `token` : Le jeton d'authentification de l'utilisateur.
    - `message-id` : L'identifiant du message à mettre à jour.
    - `content` : Le nouveau contenu du message.
- `DEL_MSG` : Supprime un message.
    - `token` : Le jeton d'authentification de l'utilisateur.
    - `message-id` : L'identifiant du message à supprimer.
- `DISCONNECTED` : Informe le serveur qu'un client s'est déconnecté.

### Format des commandes


Les commandes sont envoyées au serveur sous forme de chaînes de caractères. Chaque commande commence par l'identifiant unique de la commande suivi d'un retour à la ligne, puis de paramètres au format clé-valeur (`<clé>:valeur`). Chaque paramètre est séparé par un retour à la ligne.

Voici par exemple la construction de la commande `LOGIN` :

```
LOGIN
api-token:bOwvOhMUifIjxAFA1CXI5mcR8pB4lL697FfdXjQvs5bG...
```

Ou encore la commande `SEND_MSG` :

```
SEND_MSG
token:bOwvOhMUifIjxAFA1CXI5mcR8pB4lL697FfdXjQvs5bG...
recipient-id:42
message-length:24
content:Bonjour, comment ça va ?
```

---

### Les réponses

Après chaque commande envoyée, le serveur renvoie une réponse indiquant le résultat de l'opération. 

La réponse comprend les informations suivantes :

- **Statut** : Un [code de statut](#les-codes-de-statut)
- **Données supplémentaires** : Des informations additionnelles en fonction de la commande reçu au format clé-valeur.

Le format des réponses est similaire à celui des commandes, avec le code de statut en première ligne suivi des données supplémentaires.

#### Les codes de statut

- **200/OK** : La commande a été exécutée avec succès.
- **403/DENIED** : L'utilisateur n'a pas les permissions nécessaires pour exécuter la commande.
- **401/UNAUTH** : L'utilisateur n'est pas authentifié.
- **416/MISFMT** : La commande est mal formatée (voir le [format des commandes](#format-des-commandes)).
- **426/TOOMRQ** : Le serveur a reçu trop de messages en peu de temps (voir la configuration).
