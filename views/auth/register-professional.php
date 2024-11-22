<?php
/** @var $this \app\core\View */
/** @var $proPublic \app\forms\PublicProfessionalRegister */
/** @var $proPrivate \app\forms\PrivateProfessionalRegister */

use app\core\form\Form;

$this->title = 'RegisterProfessional';
?>

<div class="form-page">
    <div class="h-auth">
        <h1 class="heading-1">Inscription</h1>
        <div class="q-auth">
            <p>Déjà un compte ?</p>
            <a href="/connexion" class="link">Connexion</a>
        </div>
    </div>
    <x-tabs>
        <!-- public -->

        <x-tab role="heading" slot="tab">Public / associatif</x-tab>
        <x-tab-panel role="region" slot="panel">
            <?php $form = \app\core\form\Form::begin('', 'post', '', 'form-w') ?>
                <input type="hidden" name="form-name" value="public">
                <div class="form-inputs">
                    <div class="flex flex-row gap-2">
                        <input class="checkbox checkbox-normal" type="checkbox" id="asso">
                        <label class="checkbox" for="asso">Statut associatif</label>
                    </div>

                    <div class="siren hidden">
                        <?php echo $form->field($proPublic, 'siren') ?>
                    </div>

                    <?php echo $form->field($proPublic, 'denomination') ?>
                    <?php echo $form->field($proPublic, 'mail')?>
                    <?php echo $form->field($proPublic, 'phone')?>

                    <div class="flex gap-4">
                        <div class="w-25%">
                            <?php echo $form->field($proPublic, 'streetnumber')?>
                        </div>
                        <?php echo $form->field($proPublic, 'streetname')?>
                    </div>

                    <div class="flex gap-4">
                        <div class="w-25%">
                            <?php echo $form->field($proPublic, 'postaleCode')?>
                        </div>
                        <?php echo $form->field($proPublic, 'city')?>
                    </div>

                    <?php echo $form->field($proPublic, 'password')->passwordField()?>
                    <?php echo $form->field($proPublic, 'passwordConfirm')->passwordField()?>

                    <div class="flex flex-row gap-2">
                        <input class="switch" type="checkbox" id="conditions">
                        <label class="switch" for="conditions">J'accepte les <a href="" class="link">conditions générales d'utilisation</a></label>
                    </div>
                    <div class="flex flex-row gap-2">
                        <input class="switch" type="checkbox" id="notifications">
                        <label class="switch" for="notifications">J'autorise l'envoie de notifications</label>
                    </div>
                 </div>
                <button type="submit" class="button w-full">S'inscrire</button>
            <?php \app\core\form\Form::end() ?>
        </x-tab-panel>

        <!-- privé -->

        <x-tab role="heading" slot="tab">Privé</x-tab>
        <x-tab-panel role="region" slot="panel">
            <?php $form = \app\core\form\Form::begin('', 'post', '', 'form-w') ?>
                <input type="hidden" name="form-name" value="private">
                <div class="form-inputs">
                    <div class="flex gap-4">
                        <?php echo $form->field($proPrivate, 'denomination') ?>
                        <?php echo $form->field($proPrivate, 'siren') ?>
                    </div>

                    <?php echo $form->field($proPrivate, 'mail')?>
                    <?php echo $form->field($proPrivate, 'phone')?>

                    <div class="flex gap-4">
                        <div class="w-25%">
                            <?php echo $form->field($proPrivate, 'streetnumber')?>
                        </div>
                        <?php echo $form->field($proPrivate, 'streetname')?>
                    </div>

                    <div class="flex gap-4">
                        <div class="w-25%">
                            <?php echo $form->field($proPrivate, 'postaleCode')?>
                        </div>
                        <?php echo $form->field($proPrivate, 'city')?>
                    </div>

                    <?php echo $form->field($proPrivate, 'password')->passwordField()?>
                    <?php echo $form->field($proPrivate, 'passwordConfirm')->passwordField()?>

                    <div class="flex flex-row gap-2">
                        <input class="checkbox checkbox-normal" type="checkbox" id="payement">
                        <label class="checkbox" for="asso">Je souhaite rentrer mes coordonnées bancaires maintenant (possibilité de le faire plus tard)</label>
                    </div>

                    <div class="flex flex-col gap-4">
                        <div class="flex gap-4 items-center">
                            <div class="flex items-center">
                                <input class="switch" type="checkbox" id="switch-cond" name="conditions" />
                                <label class="switch" for="switch-cond"></label>
                            </div>
                            <label for="switch-period" id="switch-period-label">J'accepte les <a href="" class="link">conditions générales d'utilisation</a></label>
                        </div>

                        <div class="flex gap-4 items-center">
                            <div class="flex items-center">
                                <input class="switch" type="checkbox" id="switch-notifs" name="notifs" />
                                <label class="switch" for="switch-notifs"></label>
                            </div>
                            <label for="switch-period" id="switch-period-label">J'authorise l'envoie de notifications</label>
                        </div>
                    </div>
                </div>
                <button type="submit" class="button w-full">S'inscrire</button>
            <?php \app\core\form\Form::end() ?>
        </x-tab-panel>
    </x-tabs>
</div>
