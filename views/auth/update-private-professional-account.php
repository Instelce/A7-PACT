<?php
/** @var $proPrivate \app\forms\MemberUpdateForm */
/** @var $paymentForm \app\forms\PaymentForm */

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
                <button data-dialog-trigger="avatar-update" class="dialog-trigger button w-25% gray"><i data-lucide="pen-line"></i>Modifier mon avatar</button>
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

<!--                    <div class="flex flex-col">-->
<!--                        <h2 class="section-header font-semibold">Autorisations</h2>-->
<!--                        <div class="flex gap-4 items-center">-->
<!--                            <div class="flex items-center">-->
<!--                                <input class="switch" type="checkbox" id="switch-notifs2" name="notifs" />-->
<!--                                <label class="switch" for="switch-notifs2"></label>-->
<!--                            </div>-->
<!--                            <label for="switch-period" id="switch-period-label2">J'autorise l'envoie de notifications</label>-->
<!--                        </div>-->
<!--                    </div>-->

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

                    <div class="popup-content bg-white lg:rounded-lg lg:shadow-lg lg:p-10 flex flex-col items-center gap-6">
                        <div>
                            <h1  class="heading-1">Valider les modifications</h1>
                        </div>
                        <div class="w-[400px]" id="password-condition-utilisation">
                            <?php echo $form->field($proPrivate, 'passwordCheck')->passwordField() ?>
                        </div>
                        <div class="flex flex-col gap-4">
                            <button type="submit" class="button purple w-[400px]">Enregistrer les modifications</button>
                            <button type="button" class="button gray w-[400px]" id="closePopupSave">Annuler</button>
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
                <div class="flex flex-col gap-8 max-w-[700px]">
                    <div>
                        <h2 class="section-header font-semibold">Facturation</h2>
                        <a href="/dashboard/factures" class="button gray">Consulter mes factures</a>
                    </div>

                    <div class="flex flex-col gap-3">
                        <h2 class="section-header font-semibold">Moyen de paiement enregistré</h2>
                        <?php $form = \app\core\form\Form::begin('', 'post', '') ?>
                            <input type="hidden" name="form-name" value="update-payment">
                            <div class="flex flex-col gap-4 w-full">
                                <div class="bt-payment flex-col px-6" id="rib">
                                    <div id="payment" class="clickable">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#0332aa" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="w-[30px] h-[30px] lucide lucide-credit-card"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
                                        <p>Virement bancaire</p>
                                    </div>
                                    <div id="content-payment" class="w-full pb-8 <?php echo (MeanOfPayment::findOneByPk(PrivateProfessional::findOneByPk(Application::$app->user->account_id)->payment_id)->isRibPayment() ? "" : "hidden"); ?>">
                                        <?php echo $form->field($paymentForm, 'titular_name')?>
                                        <?php echo $form->field($paymentForm, 'iban')?>
                                        <?php echo $form->field($paymentForm, 'bic')?>
                                    </div>
                                </div>
                                <div class="bt-payment p[.8rem] flex-col px-6" id="cb">
                                    <div id="card" class="clickable">
                                        <img src="/assets/images/payment/logoVisa.png" title="logo visa" alt="visa">
                                        <img src="/assets/images/payment/logoMS.png" title="logo visa" alt="visa">
                                        <p>Carte bancaire</p>
                                    </div>
                                    <div id="content-card" class="w-full pb-8 <?php echo (MeanOfPayment::findOneByPk(PrivateProfessional::findOneByPk(Application::$app->user->account_id)->payment_id)->isCbPayment() ? "" : "hidden"); ?>">
                                        <?php echo $form->field($paymentForm, 'card_name') ?>
                                        <?php echo $form->field($paymentForm, 'card_number') ?>
                                        <div class="flex gap-4">
                                            <?php echo $form->field($paymentForm, 'expiration_date')?>
                                            <?php echo $form->field($paymentForm, 'cvv')?>
                                        </div>
                                    </div>
                                </div>
                                <div class="bt-payment flex-row justify-start px-6" id="paypal">
                                    <div class="clickable">
                                        <img src="/assets/images/payment/logoPaypal.png" title="logo paypal" alt="paypal">
                                        <p>Paypal (+1.15€)</p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col gap-4 mt-8 w-[90%]">
                                <p><?php echo $form->error($paymentForm, 'passwordCheckPayment') ?></p>
                                <button id="saveUpdatePopupTriggerPayment" type="button" class="button purple w-full">Enregistrer les modifications</button>
                            </div>
                            <div id="popupSaveUpdatePayment"
                                 class="hidden lg:fixed lg:inset-0 lg:bg-black/50 flex items-center justify-center">

                                <!--//////////////////////////////////////////////////////////////////////////
                                // save update pop up
                                //////////////////////////////////////////////////////////////////////////:-->

                                <div class="popup-content bg-white lg:rounded-lg lg:shadow-lg lg:p-10 flex flex-col items-center gap-6">
                                    <div>
                                        <h1  class="heading-1">Valider les modifications</h1>
                                    </div>
                                    <div class="w-[400px]" id="password-condition-utilisation-payment">
                                        <?php echo $form->field($paymentForm, 'passwordCheckPayment')->passwordField() ?>
                                    </div>
                                    <div class="flex flex-col gap-4">
                                        <button type="submit" class="button purple w-[400px]">Enregistrer les modifications</button>
                                        <button type="button" class="button gray w-[400px]" id="closePopupSavePayment">Annuler</button>
                                    </div>
                                </div>
                            </div>
                        <?php \app\core\form\Form::end() ?>
                    </div>
                </div>
            </x-tab-panel>
        <?php } ?>

        <x-tab role="heading" slot="tab" class="pro">
            <i data-lucide="key"></i>
            Sécurité
        </x-tab>
        <x-tab-panel role="region" slot="panel">
            <div class="flex flex-col gap-4 max-w-[700px]">
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
// Avatar pop up
//////////////////////////////////////////////////////////////////////////:-->

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
                            <img class="w-[125px] h-[125px] rounded-full object-cover" src="<?php echo Application::$app->user->avatar_url ?>">
                        </div>
                        <div class="flex justify-center items-center gap-2 w-full">
                            <label for="file" class="button gray w-[250px]">
                                <i data-lucide="upload"></i> Importer
                            </label>
                            <input id="file" class="hidden" type="file" name="avatar">
                        </div>
                    </div>
                    <div class="flex justify-center items-center gap-4 w-full">
                        <button type="button" class="dialog-close button gray w-[250px]" id="closePopupAvatar">Annuler</button>
                        <button type="submit" class="button w-[250px]">Enregistrer les modifications</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
