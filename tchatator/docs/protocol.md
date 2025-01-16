# Protocole

## Description
Ce fichier définit un protocole de communication client-serveur basé sur des commandes et des réponses échangées via un socket. Le protocole gère l'envoi et la réception de messages, ainsi que la gestion des connexions et des déconnexions.

---

## Fonctionnement du Protocole

### 1. **Processus Principal**
1. Le client se connecte au serveur.
2. Il envoie une commande (par exemple, se connecter avec un `LOGIN`).
3. Le serveur répond avec un statut et des données.
4. Le client traite la réponse et effectue les actions nécessaires (afficher un message, demander de nouvelles informations, etc.).

### 2. **Exemple de Fonctionnement**

- **Connexion** : Un client envoie la commande `LOGIN` avec un jeton API. Le serveur répond avec un code 200 si la connexion est réussie.
- **Envoi de Message** : Le client envoie une commande `SEND_MSG` avec le message et l'identifiant du destinataire. Le serveur confirme l'envoi ou renvoie une erreur si quelque chose ne va pas.

### 3. **Commandes**
Le client envoie des commandes au serveur pour réaliser diverses actions. Les commandes sont envoyées sous forme de chaînes formatées et incluent des paramètres comme le jeton d'authentification, l'identifiant du destinataire, et le contenu du message.

Voici quelques commandes disponibles :

- **LOGIN** : Authentifie un utilisateur avec un jeton API.
- **SEND_MSG** : Envoie un message à un utilisateur.
- **UPDT_MSG** : Met à jour un message existant.
- **DEL_MSG** : Supprime un message.
- **GET_MSGS** : Récupère les nouveaux messages.
- **IS_CONN** : Vérifie si un utilisateur est connecté.
- **DISCONNECTED** : Informe le serveur qu'un client s'est déconnecté.

### 4. **Réponses**
Après chaque commande envoyée, le serveur renvoie une réponse indiquant le résultat de l'opération. la réponse comprenant :

- **Statut** : Un code de statut (par exemple 200 pour OK ou 403 pour "Accès refusé").
- **Données supplémentaires** : Des informations additionnelles au format clé-valeur (par exemple, la taille du message envoyé).

### 5. **Envoi de Commandes et Réception des Réponses**

#### Envoi de Commande :
- Le client crée une commande avec un nom et des paramètres (ex. token, message).
- La commande est formatée en une chaîne de caractères et envoyée au serveur via un socket.

#### Réception de Réponse :
- Le serveur répond avec une chaîne de caractères qui inclut un code de statut et, éventuellement, des données supplémentaires.
- Le client analyse cette réponse pour en extraire les informations pertinentes.

---

## Exemple de Commandes

#### Connexion :
```
response_t* response = send_login(sock, "mon_api_token");
```


Cette commande envoie un jeton d'authentification pour connecter l'utilisateur.



```
response_t* response = send_message(sock, "mon_token", "Hello World", 123);
```

Celle-ci envoie le message "Hello World" à l'utilisateur avec l'ID 123.

```
response_t* response = send_update_message(sock, "mon_token", 456, "Updated message content");
```

Enfin, celle-ci met à jour le message avec l'ID 456.