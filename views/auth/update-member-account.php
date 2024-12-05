<?php
/** @var $model \app\forms\MemberUpdateForm */

$this->title = 'Modification compte';
$this->jsFile = 'updateMemberAccount';

use app\core\Application;
use app\core\Mailer;

?>

<div class="flex gap-2">
    <x-tabs class="column" save>
        <x-tab role="heading" slot="tab" id="info">
            <i data-lucide="user"></i>
            Informations personnelles
        </x-tab>

        <x-tab-panel role="region" slot="panel">
            <div class="flex flex-row mb-8 items-center">
                <img class="w-[125px] h-[125px] rounded-full mr-10 object-cover"
                     src="<?php echo Application::$app->user->avatar_url ?>">
                <button data-dialog-trigger="avatar-update"
                        class="dialog-trigger button w-25% gray"><i data-lucide="pen-line"></i>Modifier
                    mon avatar
                </button>
            </div>
            <?php $form = \app\core\form\Form::begin('', 'post', '', 'flex flex-col justify-center items-center') ?>
            <input type="hidden" name="form-name" value="update-main">
            <div class="flex flex-col w-full gap-6">
                <div class="form-inputs flex flex-col gap-1">
                    <div class="flex gap-4">
                        <?php echo $form->field($model, 'lastname') ?>
                        <?php echo $form->field($model, 'firstname') ?>
                    </div>

                    <?php echo $form->field($model, 'pseudo') ?>
                    <?php echo $form->field($model, 'mail') ?>
                    <?php echo $form->field($model, 'phone')->phoneField() ?>
                    <div class="flex gap-4">
                        <div>
                            <?php echo $form->field($model, 'streetNumber') ?>
                        </div>
                        <?php echo $form->field($model, 'streetName') ?>
                    </div>
                    <div class="flex gap-4">
                        <div>
                            <?php echo $form->field($model, 'postalCode') ?>
                        </div>
                        <?php echo $form->field($model, 'city') ?>
                    </div>
                </div>
                <div class="flex flex-col gap-4">
                    <div class="flex gap-4 items-center">
                        <div class="flex items-center">
                            <input class="switch" type="checkbox" id="switch-notification"
                                   name="notification"
                                   value="1" <?php echo $model->notification == 1 ? "checked" : "" ?>/>
                            <label class="switch" for="switch-notification"></label>
                        </div>
                        <label for="switch-period" id="switch-period-label">J’autorise l’envoi de
                            notifications concernant
                            la mise en ligne de nouvelles offres et autre</label>
                    </div>
                </div>

                <div class="flex flex-col gap-4 mt-4">
                    <p><?php echo $form->error($model, 'passwordCheck') ?></p>
                    <button data-dialog-trigger="password-confirm" type="button" class="button dialog-trigger">
                        Enregistrer les modifications
                    </button>
                </div>
            </div>
            <!--//////////////////////////////////////////////////////////////////////////
            // save update pop up
            ///////////////////////////////////////////////////////////////////////////-->

            <div class="dialog-container close" data-dialog-name="password-confirm">
                <div class="dialog">
                    <header class="dialog-header">
                        <h3 class="dialog-title">Valider les modifications</h3>
                    </header>

                    <div class="dialog-content">
                        <?php echo $form->field($model, 'passwordCheck')->passwordField() ?>

                        <div class="grid lg:grid-cols-2 sm:grid-cols-1 gap-4 mt-8">
                            <button type="button" class="button gray dialog-close">
                                Annuler
                            </button>
                            <button type="submit" class="button">Enregistrer les
                                modifications
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php \app\core\form\Form::end() ?>
        </x-tab-panel>

        <x-tab role="heading" slot="tab" id="securite">
            <i data-lucide="key"></i>
            Sécurité
        </x-tab>

        <x-tab-panel role="region" slot="panel">
            <div class="flex flex-col gap-4">
                <form method="post" class="flex">
                    <input type="hidden" name="form-name" value="reset-password">
                    <button id="passwordModify" type="submit" class="button w-full gray">Modifier le
                        mot de passe
                    </button>
                </form>
                <!--<button id ="accountDelete" type="submit" class="button danger">Supprimer mon compte</button>-->
            </div>
        </x-tab-panel>
    </x-tabs>
</div>

<!--//////////////////////////////////////////////////////////////////////////
// Avatar pop up
///////////////////////////////////////////////////////////////////////////-->

<div class="dialog-container close" data-dialog-name="avatar-update">
    <div class="dialog">
        <header class="dialog-header">
            <h3 class="dialog-title"> Modification de votre avatar</h3>
            <p class="dialog-description"></p>
        </header>

        <div class="dialog-content">
            <!-- Chacun mettra ce qu'il veux ici -->
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="form-name" value="update-avatar">
                <div class="grid gap-4">
                    <div class="grid gap-6 py-6">
                        <div class="flex justify-center items-center w-full">
                            <img class="w-[175px] h-[175px] rounded-full object-cover avatar-image"
                                 src="<?php echo Application::$app->user->avatar_url ?>">
                        </div>
                        <div class="flex justify-center items-center gap-2 w-full">
                            <label for="file" class="button gray w-[250px]">
                                <i data-lucide="upload"></i> Importer
                            </label>
                            <input id="file" class="hidden avatar-input" type="file" name="avatar">
                        </div>
                    </div>
                    <div class="flex justify-center items-center gap-4 w-full">
                        <button type="button" class="dialog-close button gray w-[250px]"
                                id="closePopupAvatar">Annuler
                        </button>
                        <button type="submit" class="button w-[250px]">Enregistrer les
                            modifications
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
