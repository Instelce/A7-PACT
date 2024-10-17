<?php
/** @var $model \app\models\User */
?>

<h1>login</h1>

<?php $form = \app\core\form\Form::begin('', 'post') ?>
<?php echo $form->field($model, 'email') ?>
<?php echo $form->field($model, 'password')->passwordField() ?>
<button type="submit" class="button">login</button>
<?php \app\core\form\Form::end() ?>

