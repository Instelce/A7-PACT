<?php
/** @var $proPublic \app\forms\MemberUpdateForm */

use app\core\Application;


$this->title = 'Modifier mon profil public';
$this->jsFile = 'updatePublicProfessionalAccount';

?>
<div class="flex gap-2">
    <x-tabs class="column pro">
        <x-tab role="heading" slot="tab" class="pro">
            <i data-lucide="user"></i>
            Informations personnelles
        </x-tab>

        <x-tab-panel role="region" slot="panel">
            <div class="flex flex-row mb-8 items-center">
                <img class="w-[125px] h-[125px] rounded-full mr-10 object-cover" src="<?php echo Application::$app->user->avatar_url ?>">
                <div>
                    <button id="avatarUpdate" type="button" class="button w-25% gray"><i data-lucide="pen-line"></i>Modifier mon avatar</button>
                </div>
            </div>
            <?php $form = \app\core\form\Form::begin('', 'post', '', 'form-w items-start') ?>
            <input type="hidden" name="form-name" value="update-public">

            <div class="form-inputs flex flex-col gap-8 w-full">
                <div class="flex flex-col w-full">
                    <h2 class="section-header font-semibold">Données personnelles</h2>
                    <div class="flex gap-4 on-same-line">
                        <?php echo $form->field($proPublic, 'denomination') ?>
                        <?php echo $form->field($proPublic, 'siren') ?>
                    </div>

                    <?php echo $form->field($proPublic, 'mail')?>
                    <?php echo $form->field($proPublic, 'phone')->phoneField() ?>
                </div>

                <div class="flex flex-col">
                    <h2 class="section-header font-semibold">Adresse</h2>
                    <div class="flex gap-4 on-same-line">
                        <div class="w-25%">
                            <?php echo $form->field($proPublic, 'streetnumber')?>
                        </div>
                        <?php echo $form->field($proPublic, 'streetname')?>
                    </div>

                    <div class="flex gap-4 on-same-line">
                        <div class="w-25%">
                            <?php echo $form->field($proPublic, 'postaleCode')?>
                        </div>
                        <?php echo $form->field($proPublic, 'city')?>
                    </div>
                </div>

                <div class="flex flex-col">
                    <h2 class="section-header font-semibold">Autorisations</h2>
                    <div class="flex gap-4 items-center">
                        <div class="flex items-center">
                            <input class="switch" type="checkbox" id="switch-notifs2" name="notifs" />
                            <label class="switch" for="switch-notifs2"></label>
                        </div>
                        <label for="switch-period" id="switch-period-label2">J'autorise l'envoie de notifications</label>
                    </div>
                </div>

            </div>
            <div class="flex flex-col gap-4 mt-8 w-[90%]">
                <p><?php echo $form->error($proPublic, 'passwordCheck') ?></p>
                <button id="saveUpdatePopupTrigger" type="button" class="button purple w-full">Enregistrer les modifications</button>
            </div>
            <div id="popupSaveUpdate"
                 class="hidden lg:fixed lg:inset-0 lg:bg-black/50 flex items-center justify-center">

                <!--//////////////////////////////////////////////////////////////////////////
                // save update pop up
                //////////////////////////////////////////////////////////////////////////:-->

                <div class="popup-content bg-white lg:rounded-lg lg:shadow-lg lg:max-w-[900px] lg:max-h-[225px] w-full h-full lg:p-10 flex flex-col items-center gap-6">
                    <div>
                        <h1  class="heading-1">Valider les modifications</h1>
                    </div>
                    <div class="w-[400px]" id="password-condition-utilisation">
                        <?php echo $form->field($proPublic, 'passwordCheck')->passwordField() ?>
                    </div>
                    <div class="flex flex-row gap-4">
                        <div>
                            <button type="button" class="button gray w-[400px]" id="closePopupSave">Annuler</button>
                        </div>
                        <div>
                            <button type="submit" class="button purple w-[400px]">Enregistrer les modifications</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php \app\core\form\Form::end() ?>
        </x-tab-panel>

        <x-tab role="heading" slot="tab" class="pro">
            <i data-lucide="key"></i>
            Sécurité
        </x-tab>
        <x-tab-panel role="region" slot="panel">
            <div class="flex flex-col gap-4">
                <h2 class="section-header">Mot de passe</h2>
                <form method="post" class="flex">
                    <input type="hidden" name="form-name" value="reset-password">
                    <button id ="passwordModify" type="submit" class="button w-full gray">Modifier le mot de passe</button>
                </form>
            </div>
        </x-tab-panel>
    </x-tabs>
</div>

<!--//////////////////////////////////////////////////////////////////////////
// Avatar pop up
//////////////////////////////////////////////////////////////////////////:-->

<div id="popupAvatarUpdate"
     class="hidden lg:fixed lg:inset-0 lg:bg-black/50 flex items-center justify-center">
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="form-name" value="update-avatar">
        <div
            class="popup-content bg-white lg:rounded-lg lg:shadow-lg lg:max-w-[900px] lg:max-h-[400px]
        w-full h-full p-2 lg:p-10 flex flex-col justify-center items-center gap-6">
            <div>
                <h1  class="heading-1">Ajout de votre photo de profil</h1>
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
                    <button class="button w-full gray">
                        <i data-lucide="trash"></i>
                        Supprimer
                    </button>
                </div>
            </div>
            <div class="flex flex-row gap-4">
                <div class="w-[400px]">
                    <button id="closePopupAvatar" type="button" class="button w-full gray">Annuler</button>
                </div>
                <div class="w-[400px]">
                    <button type="submit" class="button purple w-full">Enregistrer les modifications</button>
                </div>
            </div>
        </div>
    </form>
</div>

<!--//////////////////////////////////////////////////////////////////////////
// save update pop up
//////////////////////////////////////////////////////////////////////////:-->

<div id="popupSaveUpdate"
     class="hidden lg:fixed lg:inset-0 lg:bg-black/50 flex items-center justify-center">
    <div
        class="popup-content bg-white lg:rounded-lg lg:shadow-lg lg:max-w-[900px] lg:max-h-[400px]
        w-full h-full p-2 lg:p-10 flex flex-col justify-center items-center gap-6">
        <div>
            <h1  class="heading-1">Valider les modifications</h1>
        </div>
        <div>
            <img class="w-[125px] h-[125px] rounded-full object-cover" src="<?php echo Application::$app->user->avatar_url ?>">
        </div>
        <div class="flex flex-row gap-4">
            <div class="w-[200px]">
                <button type="submit" class="button w-full gray">
                    <i data-lucide="upload"></i>
                    Importer
                </button>
            </div>
            <div class="w-[200px]">
                <button type="submit" class="button w-full gray">
                    <i data-lucide="trash"></i>
                    Supprimer
                </button>
            </div>
        </div>
        <div class="flex flex-row gap-4">
            <div class="w-[400px]">
                <button type="submit" class="button w-full gray">Annuler</button>
            </div>
            <div class="w-[400px]">
                <button type="submit" class="button purple w-full">Enregistrer les modifications</button>
            </div>
        </div>
    </div>
</div>