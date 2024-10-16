# Conventions

## SQL

### Nommage

Tous les noms des tables et leurs attributs en `snake_case`.

### Tables

- Pour les tables de jointure, on ajoute les noms des tables référencées en
  `snake_case` séparés par un `_`.
- Pour les héritages, on ajoute le nom de la table fille en `snake_case` **suivi** de
  `_` et du nom de la table mère en `snake_case`.
    
    Exemple : une classe `visite` qui hérite de `offre` vas être nommée `visite_offre`.

### Attributs

- Pour le nom des foreign key, on ajoute le nom de la table référencée en
  `snake_case` suivi de `_id`.
- Pour les `url` : `VARCHAR(255)`.
- Pour les tables qui ont besoin des attributs `created_at` et `updated_at`, on les définit comme ça :
    ```
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ```

## PHP et JavaScript

Tout en `camelCase`.

