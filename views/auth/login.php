<?php
/** @var $model \app\models\User */

$this->title = 'Connexion';
?>

<div class="form form-page flex flex-col justify-center items-center">
    <div class="flex flex-col gap-0">
        <h1 class="heading-1">Connexion</h1>
        <div class="flex flex-col gap-1 justify-center items-center mt-[-20px]">
            <p>Pas de compte ?</p>
            <a href="inscription" class="link">S'inscrire</a>
        </div>
    </div>
    <?php $form = \app\core\form\Form::begin('', 'post', '', 'flex flex-col justify-center items-center') ?>
    <div class="flex flex-col w-[600px] gap-6">
        <div class="form-inputs">
            <?php echo $form->field($model, 'mail') ?>
            <?php echo $form->field($model, 'password')->passwordField() ?>
        </div>

        <button type="submit" class="button w-full ">Connexion</button>
    </div>
    <?php \app\core\form\Form::end() ?>
</div>
