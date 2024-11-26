<?php
/** @var $this \app\core\View */
/** @var $proPublic \app\forms\PublicProfessionalRegister */
/** @var $proPrivate \app\forms\PrivateProfessionalRegister */

use app\core\form\Form;

$this->title = 'RegisterProfessional';
$this->jsFile = 'registerProfessional';
?>

<div class="form-page">
    <div class="h-auth">
        <h1 class="heading-1">Inscription</h1>
        <div class="q-auth">
            <p>Déjà un compte ?</p>
            <a href="/connexion" class="link">Connexion</a>
        </div>
    </div>
    <x-tabs class="flex flex-col items-center justify-center w-[600px]">
        <!-- public -->

        <x-tab role="heading" slot="tab" class="w-full text-center">Public / associatif</x-tab>
        <x-tab-panel role="region" slot="panel" class="w-full pt-4 pb-8">
            <?php $form = \app\core\form\Form::begin('', 'post', '', 'form-w') ?>
                <input type="hidden" name="form-name" value="public">
                <div class="form-inputs w-full">
                    <div class="flex flex-row gap-2 mb-4">
                        <input class="checkbox checkbox-normal" type="checkbox" id="asso">
                        <label class="checkbox" for="asso">Statut associatif</label>
                    </div>

                    <div class="hidden" id="siren">
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

                    <div class="flex flex-col gap-4 mt-4 mb-4">
                        <div class="flex gap-4 items-center">
                            <div class="flex items-center">
                                <input class="switch" type="checkbox" id="switch-cond-public" name="conditions" />
                                <label class="switch" for="switch-cond-public"></label>
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
                <button type="submit" id="submitFormProPublic" class="button w-[90%]">S'inscrire</button>
            <?php \app\core\form\Form::end() ?>
        </x-tab-panel>

        <!-- privé -->

        <x-tab role="heading" slot="tab" class="w-full text-center">Privé</x-tab>
        <x-tab-panel role="region" slot="panel" class="w-full pt-4 pb-8">
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

                    <div class="flex flex-row gap-2 mb-4">
                        <input class="checkbox checkbox-normal" type="checkbox" id="payment">
                        <label class="checkbox" for="payment">Je souhaite rentrer mes coordonnées bancaires maintenant (possibilité de le faire plus tard)</label>
                    </div>

                    <div class="" id="mean-payment">
                        <div class="flex flex-col gap-4 w-full">
                            <div class="button payment" id="rib">
                                <div class="flex flex-row w-full gap-4 pl-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-credit-card"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
                                    Virement bancaire
                                </div>
                                <div class="w-full hidden">
                                    <?php echo $form->field($proPrivate, 'titular-account') ?>
                                    <?php echo $form->field($proPrivate, 'iban') ?>
                                    <?php echo $form->field($proPrivate, 'bic')?>
                                </div>
                            </div>
                            <div class="button payment"  id="cb">
                                <div class="flex flex-row w-full gap-4 pl-2">
                                    <img src="/html/assets/images/payment/logoVisa.png" title="logo visa" alt="visa">
                                    Carte bancaire
                                </div>
                                <div class="flex flex-row w-full hidden">
                                    <?php echo $form->field($proPrivate, 'titular-card') ?>
                                    <?php echo $form->field($proPrivate, 'cardnumber') ?>
                                    <?php echo $form->field($proPrivate, 'expirationdate')?>
                                    <?php echo $form->field($proPrivate, 'cryptogram')?>
                                </div>
                            </div>
                            <div class="button payment flex flex-row justify-start pl-2" id="paypal">
                                <img src="/html/assets/images/payment/logoPaypal.png" title="logo paypal" alt="paypal">
                                Paypal (+1.15€)
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-4 mt-2 mb-4">
                        <div class="flex gap-4 items-center">
                            <div class="flex items-center">
                                <input class="switch" type="checkbox" id="switch-cond-private" name="conditions" />
                                <label class="switch" for="switch-cond-private"></label>
                            </div>
                            <label for="switch-period" id="switch-period-label2">J'accepte les <a href="" class="link">conditions générales d'utilisation</a></label>
                        </div>

                        <div class="flex gap-4 items-center">
                            <div class="flex items-center">
                                <input class="switch" type="checkbox" id="switch-notifs2" name="notifs" />
                                <label class="switch" for="switch-notifs2"></label>
                            </div>
                            <label for="switch-period" id="switch-period-label2">J'authorise l'envoie de notifications</label>
                        </div>
                    </div>
                </div>
                <button type="submit" id="submitFormProPrivate" class="button w-[90%]">S'inscrire</button>
            <?php \app\core\form\Form::end() ?>
        </x-tab-panel>
    </x-tabs>
</div>
