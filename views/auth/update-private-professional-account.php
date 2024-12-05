<?php
/** @var $proPrivate \app\forms\MemberUpdateForm */

use app\core\Application;
    use app\models\payment\MeanOfPayment;
use app\models\user\professional\PrivateProfessional;


$this->title = 'Modifier mon profil privé';
$this->jsFile = 'updatePrivateProfessionalAccount';

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
                <input type="hidden" name="form-name" value="update-private">

                <div class="form-inputs flex flex-col gap-8 w-full">
                    <div class="flex flex-col w-full">
                        <h2 class="section-header font-semibold">Données personnelles</h2>
                        <div class="flex gap-4 on-same-line">
                            <?php echo $form->field($proPrivate, 'denomination') ?>
                            <?php echo $form->field($proPrivate, 'siren') ?>
                        </div>

                        <?php echo $form->field($proPrivate, 'mail')?>
                        <?php echo $form->field($proPrivate, 'phone')->phoneField() ?>
                    </div>

                    <div class="flex flex-col">
                        <h2 class="section-header font-semibold">Adresse</h2>
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
                    <p><?php echo $form->error($proPrivate, 'passwordCheck') ?></p>
                    <button id="saveUpdatePopupTrigger" type="button" class="button purple w-full">Enregistrer les modifications</button>
                </div>
                <div id="popupSaveUpdate"
                     class="hidden lg:fixed lg:inset-0 lg:bg-black/50 flex items-center justify-center">

                <!--//////////////////////////////////////////////////////////////////////////
                // save update pop up
                //////////////////////////////////////////////////////////////////////////:-->

                    <div class="popup-content bg-white lg:rounded-lg lg:shadow-lg lg:max-w-[900px] lg:max-h-[225px]
                                    w-full h-full lg:p-10 flex flex-col items-center gap-6">
                        <div>
                            <h1  class="heading-1">Valider les modifications</h1>
                        </div>
                        <div class="w-[400px]" id="password-condition-utilisation">
                            <?php echo $form->field($proPrivate, 'passwordCheck')->passwordField() ?>
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

        <?php if (Application::$app->user->isPrivateProfessional()) { ?>
            <x-tab role="heading" slot="tab" class="pro">
                <i data-lucide="euro"></i>
                Paiement
            </x-tab>
            <x-tab-panel role="region" slot="panel">
                <div class="flex flex-col gap-8">
                    <div>
                        <h2 class="section-header font-semibold">Facturation</h2>
                        <a href="/dashboard/factures" class="button purple">Consulter mes factures</a>
                    </div>

                    <div class="flex flex-col gap-3">
                        <h2 class="section-header font-semibold">Moyen de paiement enregistré</h2>
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

        <x-tab role="heading" slot="tab" class="pro">
            <i data-lucide="key"></i>
            Sécurité
        </x-tab>
        <x-tab-panel role="region" slot="panel">
            <div class="flex flex-col gap-4">
                <h2 class="section-header font-semibold">Mot de passe</h2>
                <form method="post" class="flex">
                    <input type="hidden" name="form-name" value="reset-password">
                    <button id ="passwordModify" type="submit" class="button w-full gray">Modifier le mot de passe</button>
                </form>
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
        <div class="popup-content bg-white lg:rounded-lg lg:shadow-lg lg:max-w-[900px] lg:max-h-[700px]
        w-full h-full p-2 lg:p-10 flex flex-col justify-center items-center gap-6">
            <div class="flex flex-col gap-4 w-full">
                <div class="bt-payment flex-col p-4" id="rib">
                    <div id="payment" class="clickable">
                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#0332aa" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="w-[30px] h-[30px] lucide lucide-credit-card"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
                        <p>Virement bancaire</p>
                    </div>
                    <div id="content-payment" class="w-full <?php echo (MeanOfPayment::findOneByPk(PrivateProfessional::findOneByPk(Application::$app->user->account_id)->payment_id)->isRibPayment() ? "" : "hidden"); ?>">
                        <?php echo $form->field($proPrivate, 'titular-account')?>
                        <?php echo $form->field($proPrivate, 'iban')?>
                        <?php echo $form->field($proPrivate, 'bic')?>
                    </div>
                </div>
                <div class="bt-payment p[.8rem] flex-col p-4" id="cb">
                    <div id="card" class="clickable">
                        <img src="/assets/images/payment/logoVisa.png" title="logo visa" alt="visa">
                        <img src="/assets/images/payment/logoMS.png" title="logo visa" alt="visa">
                        <p>Carte bancaire</p>
                    </div>
                    <div id="content-card" class="w-full <?php echo (MeanOfPayment::findOneByPk(PrivateProfessional::findOneByPk(Application::$app->user->account_id)->payment_id)->isCbPayment() ? "" : "hidden"); ?>">
                        <?php echo $form->field($proPrivate, 'titularCard') ?>
                        <?php echo $form->field($proPrivate, 'cardnumber') ?>
                        <div class="flex gap-4">
                            <?php echo $form->field($proPrivate, 'expirationdate')?>
                            <?php echo $form->field($proPrivate, 'cryptogram')?>
                        </div>
                    </div>
                </div>
                <div class="bt-payment flex-row justify-start p-4" id="paypal">
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
                    <button type="submit" class="button purple w-full">Enregistrer les modifications</button>
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
                    <button id="closePopupAvatar" type="button" class="button w-full gray">Annuler</button>
                </div>
                <div class="w-[400px]">
                    <button type="submit" class="button purple w-full">Enregistrer les modifications</button>
                </div>
            </div>
        </div>
    </form>
</div>
