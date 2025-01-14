<?php
/** @var $model User */

use app\core\form\Form;
use app\models\User;

$this->title = 'Mdp-oublie';

?>

<div class="form-page form-page-little">
    <div class="h-auth">
        <h1 class="heading-1">Mot de passe oublié</h1>
    </div>

    <?php $form = Form::begin('', 'post', '', 'form-w form-l') ?>
    <div class="form-inputs">
        <?php echo $form->field($model, 'mail') ?>
    </div>
    <button type="submit" class="button w-full ">Confirmer</button>
    <?php Form::end() ?>
</div>
