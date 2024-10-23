<?php
/** @var $this \app\core\View */
/** @var $offers \app\models\offer\Offer[] */
/** @var $offersType \app\models\offer\OfferType[] */
/** @var $photos \app\models\offer\OfferPhoto[] */

use app\core\Application;

$this->title = "Consulter mes offres";
$this->jsFile = "dashboardOffers";
$this->cssFile = "dashboardOffers";

/*var_dump($offers);
var_dump($offersType);
var_dump(count($offers));*/
?>

<body class="font-arial bg-red">
    <div class="mainContainer">
        <div class="bloc">
            <?php foreach ($offers as $i => $offer) { ?>
                <div class="imgContainer">
                    <img src="<?php echo $photos[$i]->url_photo ?>" class="w-5vw, h-vw" class="image border-gray-1">
                </div>
                <div class="description">
                    <div class="titleType">
                        <div class="title">
                            <h1><strong><?php echo $offer->title ?></strong></h1>
                        </div>
                        <span class="offersType"><?php echo $offersType[$i]->type ?></span>
                    </div>
                    <div>
                        <p class='mt-3'><?php echo $offer->summary ?></p>
                    </div>
                    <p class="location mt-3">Activités • Gratuit</p>
                    <p class="mt-2">Ploumanach • à 2 min de chez vous • Il y a 2 min</p>
                    <p class="mt-2">Mise à jour le <?php echo $offer->last_online_date ?></p>
                </div>
            <?php } ?>
        </div>
        <div class="buttons">
            <button class="button purple mt-3">
                <i data-lucide="message-square-dot"></i>
                <?php echo $offer->likes ?>
            </button>
            <button class="button gray mt-3">
                <i data-lucide="message-square-more"></i>
                <?php echo $offer->likes ?>
            </button>
            <button class="button gray mt-3">
                <i data-lucide="ban"></i>
                <?php echo $offer->likes ?>
            </button>
        </div>
    </div>
</body>
