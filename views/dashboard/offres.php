<?php
/** @var $this \app\core\View */
/** @var $offers \app\models\offer\Offer[] */
/** @var $offersType \app\models\offer\OfferType[] */

/** @var $photos \app\models\offer\OfferPhoto[] */

use app\core\Application;

$this->title = "Mes offres";
$this->jsFile = "dashboardOffers";
$this->cssFile = "dashboardOffers";

/*var_dump($offers);
var_dump($offersType);
var_dump(count($offers));*/
?>

<div class="flex gap-4">

    <!-- Tabs button -->
    <div class="flex flex-col w-[250px]">
        <div class="tab-button pro" selected>
            <i data-lucide="ticket"></i>
            Mes offres
        </div>

        <div class="tab-button pro">
            <i data-lucide="message-circle"></i>
            Avis reçus
        </div>

        <div class="tab-button pro">
            <i data-lucide="file-text"></i>
            Factures
        </div>

        <a href="/offres/creation" class="button gray icon-left mt-4">
            <i data-lucide="plus"></i>
            Créer une offre
        </a>
    </div>

    <!-- Offer cards -->
    <div class="flex flex-col">
        <?php foreach ($offers as $i => $offer) { ?>
            <article class="offer-card">
                <div class="image-container">
                    <img src="<?php echo $photos[$i]->url_photo ?>"
                         class="w-5vw, h-vw" class="image border-gray-1">
                </div>
                <div class="card-body">
                    <header>
                        <h3><?php echo $offer->title ?></h3>
                        <span class="badge blue"><?php echo $offersType[$i]->type ?></span>
                    </header>
                    <p class='mt-3'><?php echo $offer->summary ?></p>
                    <p class="location mt-3">Activités • Gratuit</p>
                    <p class="mt-2">Ploumanach • à 2 min de chez vous • Il y a 2
                        min</p>
                    <p class="mt-2">Mise à jour
                        le <?php echo $offer->last_online_date ?></p>
                </div>
            </article>

        <?php } ?>
    </div>

</div>