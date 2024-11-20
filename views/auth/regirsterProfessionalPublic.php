<?php
/** @var $model \app\models\User */

use app\core\form\Form;
use app\models\User;

$this->title = 'RegisterProfessional';
?>

<div class="form-page">
    <div class="h-auth">
        <h1 class="heading-1">S'inscrire</h1>
        <div class="q-auth">
            <p>Déjà un compte ?</p>
            <a href="connexion" class="link">Connexion</a>
        </div>
    </div>
    <?php $form = \app\core\form\Form::begin('', 'post', '', 'form-w') ?>
        <x-tabs>
            <!-- public -->

            <x-tab role="heading" slot="tab">Public / associatif</x-tab>
            <x-tab-panel role="region" slot="panel">
                <div class="form-inputs">
                    <div class="flex flex-row gap-2">
                        <input class="checkbox checkbox-normal" type="checkbox" id="asso">
                        <label class="checkbox" for="asso">Statut associatif</label>
                    </div>
                    <div class="siren hidden">
                        <?php echo $form->field($model, 'siren') ?>
                    </div>
                    <?php echo $form->field($model, 'denomination') ?>
                    <?php echo $form->field($model, 'mail')?>
                    <?php echo $form->field($model, 'streetnumber')?>
                    <?php echo $form->field($model, 'streetname')?>
                    <?php echo $form->field($model, 'postaleCode')?>
                    <?php echo $form->field($model, 'city')?>
                    <?php echo $form->field($model, 'phone')?>
                    <?php echo $form->field($model, 'password')->passwordField()?>
                    <?php echo $form->field($model, 'passwordConfirm')->passwordField()?>
                    <div class="flex flex-row gap-2">
                        <input class="switch" type="checkbox" id="conditions">
                        <label class="switch" for="conditions"></label>
                    </div>
                    <div class="flex flex-row gap-2">
                        <input class="switch" type="checkbox" id="notifications">
                        <label class="switch" for="notifications"></label>
                    </div>
                 </div>

                <button type="submit" class="button w-full">S'inscrire</button>
            </x-tab-panel>

            <!-- privé -->

            <x-tab role="heading" slot="tab">Privé</x-tab>
            <x-tab-panel role="region" slot="panel">
                <div class="form-inputs">
                    <?php echo $form->field($model, 'mail') ?>
                    <?php echo $form->field($model, 'password')->passwordField() ?>
                </div>

                <button type="submit" class="button w-full">S'inscrire</button>
            </x-tab-panel>
        </x-tabs>
    <?php \app\core\form\Form::end() ?>
</div>
