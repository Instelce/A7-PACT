<?php
/** @var $proPrivate \app\forms\MemberUpdateForm */

use app\core\Application;
    use app\models\payment\MeanOfPayment;
use app\models\user\professional\PrivateProfessional;


$this->title = 'Modifier mon profil';
$this->jsFile = 'updateProfessionalAccount';

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
                <input type="hidden" name="form-name" value="update-private">
                <div class="form-inputs">
                    <div class="flex gap-4 on-same-line">
                        <?php echo $form->field($proPrivate, 'denomination') ?>
                        <?php echo $form->field($proPrivate, 'siren') ?>
                    </div>

                    <?php echo $form->field($proPrivate, 'mail')?>
                    <?php echo $form->field($proPrivate, 'phone')->phoneField() ?>

                    <div class="flex gap-4 on-same-line">
                        <div class="w-25%">
                            <?php echo $form->field($proPrivate, 'streetnumber')?>
                        </div>
                        <?php echo $form->field($proPrivate, 'streetname')?>
                    </div>

                    <div class="flex gap-4 on-same-line">
                        <div class="w-25%">
                            <?php echo $form->field($proPrivate, 'postaleCode')?>
                        </div>
                        <?php echo $form->field($proPrivate, 'city')?>
                    </div>

                    <div class="flex gap-4 items-center">
                        <div class="flex items-center">
                            <input class="switch" type="checkbox" id="switch-notifs2" name="notifs" />
                            <label class="switch" for="switch-notifs2"></label>
                        </div>
                        <label for="switch-period" id="switch-period-label2">J'autorise l'envoie de notifications</label>
                    </div>
                </div>
                <button id="saveUpdatePopupTrigger" type="button" class="button w-full">Enregistrer les modifications</button>
            <?php \app\core\form\Form::end() ?>
        </x-tab-panel>

        <?php if (Application::$app->user->isPrivateProfessional()) { ?>
            <x-tab role="heading" slot="tab">
                <i data-lucide="euro"></i>
                Paiement
            </x-tab>
            <x-tab-panel role="region" slot="panel">
                <div class="flex flex-col gap-8">
                    <div>
                        <h2 class="section-header">Facturation</h2>
                        <a href="/dashboard/factures" class="button">Consulter mes factures</a>
                    </div>

                    <div class="flex flex-col gap-3">
                        <h2 class="section-header">Moyen de paiement enregistré :</h2>
                        <div class="flex gap-4 items-center">
                            <div class="py-4 px-16 flex gap-2 border rounded border-solid border-gray-1">
                                <?php if((MeanOfPayment::findOneByPk(PrivateProfessional::findOneByPk(Application::$app->user->account_id)->payment_id))->isRibPayment()){?>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#0332aa" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="w-[30px] h-[30px] lucide lucide-credit-card"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
                                    <p class="text-gray-4">paiment par virement bancaire</p>
                                 <?php }

                                else if((MeanOfPayment::findOneByPk(PrivateProfessional::findOneByPk(Application::$app->user->account_id)->payment_id))->isCbPayment()){?>
                                    <img src="/assets/images/payment/logoVisa.png" alt="visa" class="h-[20px] w-auto">
                                    <img src="/assets/images/payment/logoMS.png" alt="MS" class="h-[20px] w-auto">
                                    <p class="text-gray-4">paiment par carte bancaire</p>
                                <?php }

                                 else if((MeanOfPayment::findOneByPk(PrivateProfessional::findOneByPk(Application::$app->user->account_id)->payment_id))->isPaypalPayment()){?>
                                    <img src="/assets/images/payment/logoPaypal.png" alt="visa" class="w-[30px]">
                                    <p class="text-gray-4">paiment par paypal</p>
                                <?php }

                                 else { ?>
                                     <p>Aucun moyen de paiement</p>
                                <?php }?>
                            </div>
                            <button type="button" id="buttonPayment" class="button gray">Modifier</button>
                        </div>
                    </div>
                </div>
            </x-tab-panel>
        <?php } ?>

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
// payment pop up
//////////////////////////////////////////////////////////////////////////:-->

<div id="popupOfPayment"
     class="hidden lg:fixed lg:inset-0 lg:bg-black/50 flex items-center justify-center">
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="form-name" value="update-payment">
        <div class="popup-content bg-white lg:rounded-lg lg:shadow-lg lg:max-w-[900px] lg:max-h-[600px]
        w-full h-full p-2 lg:p-10 flex flex-col justify-center items-center gap-6">
            <div class="flex flex-col gap-4 w-full">
                <div class="bt-payment flex-col" id="rib">
                    <div id="payment" class="clickable">
                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#0332aa" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="w-[30px] h-[30px] lucide lucide-credit-card"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
                        <p>Virement bancaire</p>
                    </div>
                    <div id="content-payment" class="w-full hidden">
                        <?php echo $form->field($proPrivate, 'titular-account')?>
                        <?php echo $form->field($proPrivate, 'iban')?>
                        <?php echo $form->field($proPrivate, 'bic')?>
                    </div>
                </div>
                <div class="bt-payment p[.8rem] flex-col" id="cb">
                    <div id="card" class="clickable">
                        <img src="/assets/images/payment/logoVisa.png" title="logo visa" alt="visa">
                        <img src="/assets/images/payment/logoMS.png" title="logo visa" alt="visa">
                        <p>Carte bancaire</p>
                    </div>
                    <div id="content-card" class="w-full hidden">
                        <?php echo $form->field($proPrivate, 'titular-card') ?>
                        <?php echo $form->field($proPrivate, 'cardnumber') ?>
                        <div class="flex gap-4">
                            <?php echo $form->field($proPrivate, 'expirationdate')?>
                            <?php echo $form->field($proPrivate, 'cryptogram')?>
                        </div>
                    </div>
                </div>
                <div class="bt-payment flex-row justify-start" id="paypal">
                    <div class="clickable">
                        <img src="/assets/images/payment/logoPaypal.png" title="logo paypal" alt="paypal">
                        <p>Paypal (+1.15€)</p>
                    </div>
                </div>
            </div>

            <div class="flex flex-row gap-4">
                <div class="w-[400px]">
                    <button id="closePopup" type="button" class="button w-full gray">Annuler</button>
                </div>
                <div class="w-[400px]">
                    <button type="submit" class="button w-full">Enregistrer les modifications</button>
                </div>
            </div>
        </div>
    </form>
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