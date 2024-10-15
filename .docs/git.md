# Git manipulation

## Ajouter mes modifications sur le github

1. Syncroniser votre **fork** avec le repo original (celui de victor) en clickant sur le bouton `Sync fork` sur la page de **votre fork**.
2. Syncroniser votre branche avec la branche `main` de votre **fork** avec la commande `git pull`.
3. Ajouter vos modifications avec la commande `git add .`.
4. Commiter vos modifications avec la commande `git commit -m "feat: message de commit"`. Suivez les [conventions de commit](https://gist.github.com/qoomon/5dfcdf8eec66a051ecd85625518cfd13).
5. Pusher vos modifications avec la commande `git push`.
6. Créer une **pull request** sur la page de votre fork en cliquant sur le bouton `Contribute` puis sur `Open pull request`.
7. Mergez votre pull request en cliquant sur le bouton `Merge pull request`.

## Récupérer les modifications des autres

1. Syncroniser votre **fork** avec le repo original (celui de victor) en clickant sur le bouton `Sync fork` sur la page de **votre fork**.
2. Syncroniser votre branche avec la branche `main` de votre **fork** avec la commande `git pull`.

**OU alors**

Installez [gh](https://github.com/cli/cli#installation). Puis lancer la commande suivante en remplacant `<votre-nom-github>`.
```shell
gh repo sync <votre-nom-github>/A7-PACT -b main ; git pull
```

### Gérer les conflits

1. Si vous avez des conflits, git vous le dira avec un `Aborting` et vous devrez les résoudre.
2. Faire un `git stash` pour **sauvegarder** vos modifications.
3. Faire un `git pull` pour récupérer les modifications.
4. Faire un `git stash pop` pour **remettre** vos modifications.
5. **Résoudre** les conflits.
6. Faire un `git add .` pour ajouter les modifications.
7. Faire un `git commit -m "fix: resolve merge conflics"` pour commiter les modifications.
8. Faire un `git push` pour pusher les modifications.