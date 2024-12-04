<?php
/** @var $this \app\core\View */
/** @var $offers Offer[] */
/** @var $offersType \app\models\offer\OfferType[] */
/** @var $offersSubscription \app\models\offer\Subscription[] */
/** @var $specificData \app\core\DBModel */

/** @var $photos \app\models\offer\OfferPhoto[] */

use app\core\Application;
use app\core\Utils;
use app\models\offer\Offer;

$this->title = "Mes offres";
$this->jsFile = "dashboard/offers";
$this->cssFile = "dashboard/offers";

?>

<div class="flex gap-8">

    <!-- Next monday for input to set the launch date for option creation -->
    <input type="hidden" id="next-monday" value="<?php echo date('Y-m-d', strtotime('next monday')) ?>">

    <!-- Tabs button -->
    <div class="flex flex-col min-w-[250px] h-fit sticky top-navbar-height">

        <div class="pro-name">
            <h1><?php echo Application::$app->user->specific()->denomination ?></h1>
        </div>

        <div class="tab-button pro" selected>
            <i data-lucide="ticket"></i>
            Mes offres
        </div>

        <a href="/dashboard/avis">
            <div class="tab-button pro">
                <i data-lucide="message-circle"></i>
                Avis reçus
            </div>
        </a>

        <?php if (Application::$app->user->isPrivateProfessional()) { ?>
            <a href="/dashboard/factures">
                <div class="tab-button pro">
                    <i data-lucide="file-text"></i>
                    Factures
                </div>
            </a>
        <?php } ?>

        <a href="/offres/creation" class="button gray icon-left mt-4">
            <i data-lucide="plus"></i>
            Créer une offre
        </a>
    </div>

    <div class="page-content">
        <header>
            <x-input rounded>
                <i slot="icon-left" data-lucide="search" class="w-[18px] h-[18px]"></i>
                <input slot="input" id="search-input" type="text" placeholder="Rechercher une de vos offres">
            </x-input>

            <x-select id="offer-type" rounded>
                <span slot="trigger">Abonnement</span>
                <div slot="options">
                    <div data-value="" class="selected">Tous</div>
                    <div data-value="2">Standard</div>
                    <div data-value="3">Premium</div>
                </div>
            </x-select>
        </header>

        <!-- All offers cards generated in JS -->
        <div id="offers-container" class="flex flex-col w-full">
            <div class="loader-section"></div>
        </div>
    </div>
</div>

