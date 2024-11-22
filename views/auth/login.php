<?php
/** @var $model User */

use app\core\form\Form;
use app\models\User;

$this->title = 'Connexion';

?>

<div class="form-page form-page-little">
    <div class="h-auth">
        <h1 class="heading-1">Connexion</h1>
        <div class="q-auth">
            <p>Pas de compte ?</p>
            <a href="/inscription" class="link">S'inscrire</a>
        </div>
    </div>
    <?php $form = Form::begin('', 'post', '', 'form-w') ?>
        <div class="form-inputs">
            <?php echo $form->field($model, 'mail') ?>
            <?php echo $form->field($model, 'password')->passwordField() ?>
        </div>

        <button type="submit" class="button w-full ">Connexion</button>
    <?php Form::end() ?>
</div>
