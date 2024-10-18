<?php
/** @var $model \app\models\User */
?>
<div id="contain-log">
    <h1 class="head-1">Connexion</h1>

    <?php $form = \app\core\form\Form::begin('', 'post') ?>
    <?php echo $form->field($model, 'email') ?>
    <?php echo $form->field($model, 'password')->passwordField() ?>
    <button type="submit" class="button">Connexion</button>
    <?php \app\core\form\Form::end() ?>
</div>


