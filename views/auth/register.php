<?php
/** @var $model \app\models\User */
$this->title = 'Register';
?>

<div class="flex flex-col items-center justify-center form form-page gap-12">
    <div class="h-auth">
        <h1 class="heading-1">S'inscrire</h1>
        <div class="q-auth">
            <p>Déjà un compte ?</p>
            <a href="/../../connexion" class="link">Connexion</a>
        </div>
    </div>
    <div class="form-w">
        <a class="w-full" href="inscription/membre"></a><button class="button w-full">Membre</button>

        <a class="w-full" href="inscription/professionnel/public"><button class="button w-full gray">Professionnel</button></a>
    </div>
</div>
