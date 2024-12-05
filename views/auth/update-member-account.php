<?php
/** @var $model \app\forms\MemberUpdateForm */

$this->title = 'Inscription update';
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
                <div class="lg:hidden sm:block">
                    <form method="post" class="flex">
                        <input type="hidden" name="form-name" value="reset-password">
                        <button id ="passwordModify" type="submit" class="button w-full gray">Modifier le mot de passe</button>
                    </form>
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
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="form-name" value="update-main">

                            <?php echo $form->field($model, 'passwordCheck')->passwordField() ?>

                            <div class="grid lg:grid-cols-2 sm:grid-cols-1 gap-4 mt-8">
                                <button type="button" class="button gray dialog-close">
                                    Annuler
                                </button>
                                <button type="submit" class="button">Enregistrer les
                                    modifications
                                </button>
                            </div>
                        </form>
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
// Avatar pop up v1
///////////////////////////////////////////////////////////////////////////-->

<!--<div id="popupAvatarUpdate"
     class="hidden lg:fixed lg:inset-0 lg:bg-black/50 flex items-center justify-center">
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="form-name" value="update-avatar">
        <div
            class="popup-content bg-white lg:rounded-lg lg:shadow-lg lg:max-w-[900px] lg:max-h-[400px]
            w-full h-full p-2 lg:p-10 flex flex-col justify-center items-center gap-6">
            <div>
                <h1  class="heading-1">Modification de votre photo de profil</h1>
            </div>
            <div>
                <img class="w-[125px] h-[125px] rounded-full object-cover" src="<?php echo Application::$app->user->avatar_url ?>">
            </div>
            <div class="flex flex-row gap-4">
                <div class="w-[200px]">
                    <label for="file" class="button w-full gray">
                        <i data-lucide="upload"></i> Importer
                    </label>
                    <input id="file" class="hidden" type="file" name="avatar">
                </div>
                <div class="w-[200px]">
                    <button class="button danger w-full gray">
                        <i data-lucide="trash"></i>
                        Supprimer
                    </button>
                </div>
            </div>
            <div class="flex gap-4">
                <div class="w-[400px]">
                    <button type="button" class="button w-full gray" id="closePopupAvatar">Annuler</button>
                </div>
                <div class="w-[400px]">
                    <button type="submit" class="button w-full">Enregistrer les modifications</button>
                </div>
            </div>
        </div>
    </form>
</div>-->

<!--//////////////////////////////////////////////////////////////////////////
// Avatar pop up v2
///////////////////////////////////////////////////////////////////////////-->

<!--<form method="POST" enctype="multipart/form-data" id="popupAvatarUpdate"
      class="hidden lg:fixed lg:inset-0 lg:bg-black/50 flex items-center justify-center ">
    <input type="hidden" name="form-name" value="update-avatar">
    <div class="grid gap-4 py-6 bg-white flex items-center justify-center rounded-lg w-[500px]">
        <div class="heading-2 w-[200px]">
            Modification de votre photo de profil
        </div>
        <div class="flex justify-center items-center w-full">
            <img class="w-[125px] h-[125px] rounded-full object-cover" src="<?php echo Application::$app->user->avatar_url ?>">
        </div>
        <div class="flex justify-center items-center gap-2 w-full">
            <div class="">
                <label for="file" class="button gray">
                    <i data-lucide="upload"></i> Importer
                </label>
                <input id="file" class="hidden" type="file" name="avatar">
            </div>
            <div class="">
                <button class="button danger gray">
                    <i data-lucide="trash"></i>
                    Supprimer
                </button>
            </div>-
        </div>
        <div class="flex justify-center items-center gap-2 w-full">
            <div class="">
                <button type="button" class="button gray" id="closePopupAvatar">Annuler</button>
            </div>
            <div class="">
                <button type="submit" class="button">Enregistrer les modifications</button>
            </div>
        </div>
    </div>
</form>-->

<!--//////////////////////////////////////////////////////////////////////////
// Avatar pop up v3
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


<!--//////////////////////////////////////////////////////////////////////////
// Delete account
///////////////////////////////////////////////////////////////////////////-->

<!--<div id="popupAccountDelete"
     class="hidden lg:fixed lg:inset-0 lg:bg-black/50 flex items-center justify-center">
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="form-name" value="delete-account">
        <div
            class="popup-content bg-white lg:rounded-lg lg:shadow-lg lg:max-w-[900px] lg:max-h-[400px]
            w-full h-full p-2 lg:p-10 flex flex-col justify-center items-center gap-6">
            <div>
                <h1  class="heading-1">Demande de suppression</h1>
            </div>
            <div class="w-[400px]" id="password-delete-account">
                <?php echo $form->field($model, 'passwordCheckdd')->passwordField() ?>
            </div>
            <div class="flex gap-4">
                <div class="w-[400px]">
                    <button type="button" class="button w-full gray" id="closePopupDelete">Annuler</button>
                </div>
                <div class="w-[400px]">
                    <button type="submit" class="button danger w-full">Valider</button>
                </div>
            </div>
        </div>
    </form>
</div>-->