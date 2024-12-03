<?php
/** @var $proPublic \app\forms\PublicProfessionalUpdateForm */

$this->title = 'update-public-professional-account';
$this->jsFile = 'updateAccount';

use app\core\Application;

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
                <div>
                    <button id="avatarUpdate" type="button" class="button w-25% gray"><i data-lucide="pen-line"></i>Modifier mon avatar</button>
                </div>
            </div>
            <?php $form = \app\core\form\Form::begin('', 'post', '', 'form-w') ?>
                <input type="hidden" name="form-name" value="update-public">
                <div class="form-inputs w-full">
                    <?php if($proPublic->siren !=''){echo $form->field($proPublic, 'siren');} ?>
                    <?php echo $form->field($proPublic, 'denomination') ?>
                    <?php echo $form->field($proPublic, 'mail')?>
                    <?php echo $form->field($proPublic, 'phone')->phoneField()?>

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

                    <div class="flex gap-4 items-center">
                        <div class="flex items-center">
                            <input class="switch" type="checkbox" id="switch-notifs2" name="notifs" />
                            <label class="switch" for="switch-notifs2"></label>
                        </div>
                        <label for="switch-period" id="switch-period-label2">J'autorise l'envoie de notifications</label>
                    </div>
                </div>
                <div class="flex gap-4, mt-8">
                    <button id="saveUpdatePopupTrigger" type="button" class="button w-full">Enregistrer les modifications</button>
                </div>
            <?php \app\core\form\Form::end() ?>
        </x-tab-panel>

        <x-tab role="heading" slot="tab">
            <i data-lucide="key"></i>
            Sécurité
        </x-tab>
        <x-tab-panel role="region" slot="panel">
            <div class="flex flex-col gap-4">
                <button id ="passwordModify" type="submit" class="button w-full gray">Modifier le mot de passe</button>
                <button id ="accountDelete" type="submit" class="button w-full danger">Supprimer mon compte</button>
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
                <button type="submit" class="button w-full">Enregistrer les modifications</button>
            </div>
        </div>
    </div>
</div>

<!--//////////////////////////////////////////////////////////////////////////
// Modify Password page
//////////////////////////////////////////////////////////////////////////:-->


<!-- <div id="popupPasswordModify" class="hidden lg:fixed lg:inset-0 lg:bg-black/50 flex items-center justify-center">
    <div class="popup-content bg-white lg:rounded-lg lg:shadow-lg lg:max-w-[900px] lg:max-h-[400px] w-full h-full p-2 lg:p-10 flex flex-col justify-center items-center gap-6">
        <div>
            <h1  class="heading-1"></h1>
        </div>
        <div>
            <form type="text">test</form>
        </div>
        <div class="flex flex-row gap-4">
            <div class="w-[400px]">
                <button id="closePasswordModify" type="submit" class="button w-full gray">Annuler</button>
            </div>
            <div class="w-[400px]">
                <button type="submit" class="button w-full danger">Supprimer le compte</button>
            </div>
        </div>
    </div>
</div> -->

<!--//////////////////////////////////////////////////////////////////////////
// Delete Account pop up
//////////////////////////////////////////////////////////////////////////:-->


<div id="popupAccountDelete" class="hidden lg:fixed lg:inset-0 lg:bg-black/50 flex items-center justify-center">
    <div class="popup-content bg-white lg:rounded-lg lg:shadow-lg lg:max-w-[900px] lg:max-h-[400px] w-full h-full p-2 lg:p-10 flex flex-col justify-center items-center gap-6">
        <div>
            <h1  class="heading-1">Suppression définitive du compte</h1>
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