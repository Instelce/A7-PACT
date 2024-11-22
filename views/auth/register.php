<?php
/** @var $model \app\models\User */
$this->title = 'Register';
?>

<div class="flex flex-col items-center justify-center form form-page form-page-little gap-12">
    <div class="h-auth">
        <h1 class="heading-1">S'inscrire</h1>
        <div class="q-auth">
            <p>Déjà un compte ?</p>
            <a href="/connexion" class="link">Connexion</a>
        </div>
    </div>
    <div class="form-w">
        <a class="button w-full" href="/inscription/membre">Membre</a>

        <a class="button gray w-full" href="/inscription/professionnel">Professionnel</a>
    </div>
</div>
