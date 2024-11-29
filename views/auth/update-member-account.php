<?php
/** @var $model \app\forms\MemberUpdateForm */

$this->title = 'Inscription update';
$this->jsFile = 'updateMemberAccount';

use app\core\Application;
use app\core\Mailer;

?>
<div class="flex gap-2">
    <x-tabs class="column">
        <x-tab role="heading" slot="tab">
            <i data-lucide="user"></i>
            Informations personnelles
        </x-tab>

        <x-tab-panel role="region" slot="panel">
            <div class="flex flex-row mb-8 items-center">
                <img class="w-[125px] h-[125px] rounded-full mr-10 object-cover" src="<?php echo Application::$app->user->avatar_url ?>">
                <button id="avatarUpdate" type="button" class="button w-25% gray"><i data-lucide="pen-line"></i>Modifier mon avatar</button>
            </div>
            <?php $form = \app\core\form\Form::begin('', 'post', '', 'flex flex-col justify-center items-center') ?>
            <input type="hidden" name="form-name" value="update-main">
            <div class="flex flex-col w-full gap-6">
                <div class="form-inputs">
                    <div class="flex gap-4">
                        <?php echo $form->field($model, 'lastname') ?>
                        <?php echo $form->field($model, 'firstname') ?>
                    </div>

                    <?php echo $form->field($model, 'pseudo') ?>
                    <?php echo $form->field($model, 'mail') ?>
                    <?php echo $form->field($model, 'phone') ?>
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
                                   name="notification" value="true"/>
                            <label class="switch" for="switch-notification"></label>
                        </div>
                        <label for="switch-period" id="switch-period-label">J’autorise l’envoi de
                            notifications concernant
                            la mise en ligne de nouvelles offres et autre</label>
                    </div>
                </div>

                <div class="flex gap-4, mt-8">
                    <button id="saveUpdatePopupTrigger" type="button" class="button w-full">Enregistrer les modifications</button>
                </div>
            </div>
            <?php \app\core\form\Form::end() ?>
        </x-tab-panel>

        <?php if (Application::$app->user->isProfessional()) { ?>
            <x-tab role="heading" slot="tab">
                <i data-lucide="euro"></i>
                Paiement
            </x-tab>
            <?php } ?>

        <x-tab role="heading" slot="tab">
            <i data-lucide="key"></i>
            Sécurité
        </x-tab>
        <x-tab-panel role="region" slot="panel">
            <div class="flex flex-col gap-4">
                <form method="post">
                    <input type="hidden" name="form-name" value="reset-password">
                                <button id ="passwordModify" type="submit" class="button w-full gray">Modifier le mot de passe</button>
                </form>
<!--            <button id ="accountDelete" type="submit" class="button w-full danger">Supprimer mon compte</button> -->
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
                    <button type="button" class="button w-full gray">Annuler</button>
                </div>
                <div class="w-[400px]">
                    <button type="submit" class="button w-full">Enregistrer les modifications</button>
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
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="form-name" value="update-avatar">
        <div
            class="popup-content bg-white lg:rounded-lg lg:shadow-lg lg:max-w-[900px] lg:max-h-[400px]
        w-full h-full p-2 lg:p-10 flex flex-col justify-center items-center gap-6">
            <div>
                <h1  class="heading-1">Valider les modifications</h1>
            </div>
            <div class="w-[400px]" id="password-condition-utilisation">
                <?php echo $form->field($model, 'password')->passwordField() ?>
            </div>
            <div class="flex flex-row gap-4">
                <div class="w-[400px]">
                    <button type="button" class="button w-full gray">Annuler</button>
                </div>
                <div class="w-[400px]">
                    <button type="submit" class="button w-full">Enregistrer les modifications</button>
                </div>
            </div>
        </div>
    </form>
</div>

<!--//////////////////////////////////////////////////////////////////////////
// Modify Password page
//////////////////////////////////////////////////////////////////////////:-->


<div id="popupPasswordModify" class="hidden lg:fixed lg:inset-0 lg:bg-black/50 flex items-center justify-center">
    <div class="popup-content bg-white lg:rounded-lg lg:shadow-lg lg:max-w-[900px] lg:max-h-[400px] w-full h-full p-2 lg:p-10 flex flex-col justify-center items-center gap-6">
        <div>
            <h1  class="heading-1">Modification du mot de passe</h1>
        </div>
        <div>
            <form type="text">test</form>
        </div>
        <div class="flex flex-row gap-4">
            <div class="w-[400px]">
                <button id="closePasswordModify" type="submit" class="button w-full gray">Annuler</button>
            </div>
            <div class="w-[400px]">
                <button type="submit" class="button w-full">confirmer</button>
            </div>
        </div>
    </div>
</div> 

<!--//////////////////////////////////////////////////////////////////////////
// Delete Account pop up
//////////////////////////////////////////////////////////////////////////:-->

<!--
<div id="popupAccountDelete" class="hidden lg:fixed lg:inset-0 lg:bg-black/50 flex items-center justify-center">
    <div class="popup-content bg-white lg:rounded-lg lg:shadow-lg lg:max-w-[900px] lg:max-h-[400px] w-full h-full p-2 lg:p-10 flex flex-col justify-center items-center gap-6">
        <div>
            <h1 class="heading-1">Suppression définitive du compte</h1>
        </div>
        <div>
            <x-input>
                <input class="search-input" slot="input" type="text" placeholder="Rechercher">
                <button slot="button" class="button only-icon sm">
                </button>
            </x-input>
        </div>
        <div class="flex flex-row gap-4">
            <div class="w-[400px]">
                <button id="closeAccountDelete" type="submit" class="button w-full gray">Annuler</button>
            </div>
            <div class="w-[400px]">
                <button type="submit" class="button w-full danger">Supprimer le compte</button>
            </div>
        </div>
    </div>
</div>
        -->