<?php
/** @var $offer \app\models\offer\Offer */
/** @var $professional \app\models\user\professional\ProfessionalUser */
/** @var $address \app\models\Address */
/** @var $payment \app\forms\PaymentForm */

/** @var $this \app\core\View */

use app\core\form\Form;

$this->title = 'Payment';
$this->jsFile = "offer/payment";
?>

<header class="mb-8">
    <h1 class="heading-2 mb-4 font-title">Payment de votre offre <span
            class="underline"><?php echo $offer->title ?></span></h1>

    <p>Offre avec l'abonnement <?php echo $offer->type()->type ?>.</p>

    <?php if ($offer->option()) { ?>
        <p>Avec l'option <?php echo $offer->option()->type ?></p>
    <?php } ?>
</header>

<div class="grid grid-cols-5 gap-8">

    <div class="col-span-3">
        <div class="mb-8">
            <h2 class="section-header">Adresse de facturation</h2>

            <?php $addressForm = Form::begin('', 'post') ?>
            <div class="flex flex-col gap-2">
                <div class="flex gap-4">
                    <?php echo $addressForm->field($address, 'number', 'w-[200px]') ?>
                    <?php echo $addressForm->field($address, 'street') ?>
                </div>
                <div class="flex gap-4">
                    <?php echo $addressForm->field($address, 'postal_code', 'w-[200px]') ?>
                    <?php echo $addressForm->field($address, 'city') ?>
                </div>
            </div>
            <?php Form::end(); ?>
        </div>

        <div>
            <h2 class="section-header">Méthodes de paiement</h2>

            <?php $paymentForm = Form::begin('', 'post') ?>
            <div id="mean-payment">
                <div class="flex flex-col gap-4 w-full">
                    <div class="bt-payment flex-col" id="rib">
                        <div id="payment" class="clickable">
                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30"
                                 viewBox="0 0 24 24" fill="none" stroke="#0332aa" stroke-width="1.5"
                                 stroke-linecap="round" stroke-linejoin="round"
                                 class="w-[30px] h-[30px] lucide lucide-credit-card">
                                <rect width="20" height="14" x="2" y="5" rx="2"/>
                                <line x1="2" x2="22" y1="10" y2="10"/>
                            </svg>
                            Virement bancaire
                        </div>
                        <div id="content-payment" class="w-full hidden flex flex-col gap-1">
                            <?php echo $paymentForm->field($payment, 'titular_name') ?>
                            <?php echo $paymentForm->field($payment, 'iban') ?>
                            <?php echo $paymentForm->field($payment, 'bic') ?>
                        </div>
                    </div>
                    <div class="bt-payment p[.8rem] flex-col" id="cb">
                        <div id="card" class="clickable">
                            <img src="/assets/images/payment/logoVisa.png" title="logo visa"
                                 alt="visa">
                            <img src="/assets/images/payment/logoMS.png" title="logo visa"
                                 alt="visa">
                            Carte bancaire
                        </div>
                        <div id="content-card" class="w-full flex flex-col gap-1">
                            <?php echo $paymentForm->field($payment, 'card_name') ?>
                            <?php echo $paymentForm->field($payment, 'card_number') ?>
                            <div class="flex gap-4">
                                <?php echo $paymentForm->field($payment, 'expiration_date') ?>
                                <?php echo $paymentForm->field($payment, 'cvv') ?>
                            </div>
                        </div>
                    </div>
                    <div class="bt-payment flex-row justify-start" id="paypal">
                        <div class="clickable">
                            <img src="/assets/images/payment/logoPaypal.png" title="logo paypal"
                                 alt="paypal">
                            Paypal (+1.15€)
                        </div>
                    </div>
                </div>
            </div>
            <?php Form::end(); ?>
        </div>
    </div>

    <!-- Resume -->
    <div class="flex flex-col gap-2 col-span-2 mt-2">
        <h3 class="font-bold indent-6">Résumé</h3>

        <div class="px-6 py-4 border border-solid border-gray-1 rounded-3xl gap-1">
            <div class="flex justify-between text-gray-4">
                <p>TVA</p>
                <span>20%</span>
            </div>

            <div class="flex justify-between text-gray-4 mt-2">
                <p>Coût HT de l’offre</p>
                <span id="price-without-option"><?php echo $offer->type()->price ?>€</span>
            </div>
            <div class="flex justify-between font-bold">
                <p>Coût TTC de l'offre</p>
                <span id="price-total"><?php echo round($offer->type()->price * 1.2, 2) ?>€</span>
            </div>

            <?php if ($offer->option()) { ?>
                <div class="flex justify-between text-gray-4 mt-2">
                    <p>Coût HT de l'option</p>
                    <span class="flex">
                                <span id="price-option"><?php echo $offer->option()->price() ?>€</span>
                            </span>
                </div>
                <div class="flex justify-between font-bold">
                    <p>Coût TTC de l'option</p>
                    <span class="flex">
                                <span id="price-total-option"><?php echo round($offer->option()->price() * 1.2, 2) ?>€</span>
                            </span>
                </div>
            <?php } ?>

        </div>

        <p class="mx-6 text-gray-3">L'offre sera facturé a la <strong>journée</strong> et l'option à
            la
            <strong>semaine</strong>. Vous pourrez mettre l'offre hors ligne et ainsi arrêté la
            facturation.</p>

        <div class="mt-6">
            <a href="/dashboard" class="button purple">Payer</a>

            <p class="mx-6 mt-4 text-gray-3">En cliquant sur "Payer" je reconnais avoir lu et accepté les
                <a href="" class="underline">termes et conditions</a>, et la <a href="" class="underline">politique de confidentialité</a>.</p>
        </div>
    </div>
</div>

