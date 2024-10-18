<?php
/** @var $model \app\models\User */
?>

<div class="form-container form-page">
    <h1 class="heading-1">Connexion</h1>

    <?php $form = \app\core\form\Form::begin('', 'post') ?>

    <div class="form-inputs">
        <?php echo $form->field($model, 'email') ?>
        <?php echo $form->field($model, 'password')->passwordField() ?>
    </div>

    <button type="submit" class="button">Connexion</button>
    <?php \app\core\form\Form::end() ?>
</div>
