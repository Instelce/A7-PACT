<?php
/** @var $this \app\core\View */
/** @var $hash string */
/** @var $model \app\forms\DeleteForm */

$model = new \app\forms\DeleteForm();

use app\core\form\Form;

$this->title = 'DeleteAccount';

?>
<div class="form-page form-page-little">
    <h1 class="heading-1">suppression du compte</h1>
    <?php $form = Form::begin('', 'post', '', 'form-inputs') ?>
    <?php echo $form->field($model, 'password')->passwordField() ?>
    <button id="passwordModify" type="submit" class="button danger w-full gray">Supprimer mon compte</button>
    <?php Form::end() ?>
</div>