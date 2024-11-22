<?php
/** @var $this \app\core\View */
/** @var $offers Offer[] */
/** @var $offersType \app\models\offer\OfferType[] */
/** @var $offersOption \app\models\offer\OfferOption[] */
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

    <!-- Tabs button -->
    <div class="flex flex-col w-[250px] h-fit sticky top-navbar-height">

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
        <a href="/dashboard/factures">
            <div class="tab-button pro">
                <i data-lucide="file-text"></i>
                Factures
            </div>
        </a>
        <a href="/offres/creation" class="button gray icon-left mt-4">
            <i data-lucide="plus"></i>
            Créer une offre
        </a>
    </div>

    <div class="flex flex-col">
        <!-- Offer cards -->
        <?php foreach ($offers as $i => $offer) {
            $type = $offersType[$i]->type;
            $option = $offersOption[$i];
            if ($offer->minimum_price === null || $offer->minimum_price === 0) {
                $price = 'Gratuit';
            } elseif ($offer->category === 'restaurant') {
                $price = implode('', array_fill(0, $specificData[$i]->range_price, '€'));
            } else {
                $price = 'A partir de ' . $offer->minimum_price . ' €';
            }
            ?>
            <article class="offer-card">
                <div class="image-container">
                    <img src="<?php echo $photos[$i]->url_photo ?>"
                         onerror="this.src = 'https://placehold.co/100'">
                </div>

                <div class="card-body">
                    <header>
                        <h3 class="title"><a href="/offres/<?php echo $offer->id ?>"><?php echo $offer->title ?></a></h3>
                        <span
                            class="badge <?php echo $type === 'standard' ? 'blue' : 'yellow' ?>"><?php echo ucfirst($type) ?></span>
                    </header>

                    <p class="mt-2"><?php echo $offer->summary ?></p>

                    <div class="flex flex-col gap-2 mt-4">
                        <p class="text-gray-4 flex items-center gap-2"><?php echo Offer::frenchCategoryName($offer->category) ?>
                            <span class="dot"></span> <?php echo $price ?> <span
                                class="dot"></span> <?php echo $offer->likes . ' likes' ?> <span class="dot"></span> <?php echo $offer->opinionsCount() ?> avis
                        </p>
                        <p class="text-gray-4">Mis à jour
                            le <?php echo Utils::formatDate($offer->updated_at); ?></p>
                    </div>

_                    <!-- Option -->
                    <?php if ($option) { ?>
                        <div class="card-option">
                            <div>
                                <p class="flex gap-1">Avec l'option <span
                                        class="underline"><?php echo Utils::formatTypeString($option->type) ?></span>
                                </p>
                                <p class="text-gray-4">
                                    Du <?php echo Utils::formatDate($option->launch_date); ?>
                                    au <?php

                                    //Création de la première date
                                    $date = new DateTime($option->launch_date);

                                    // Création de l'intervalle à ajouter
                                    $interval = DateInterval::createFromDateString($option->duration * 7 . " days");

                                    // Addition de l'intervalle à la date
                                    $date->add($interval);

                                    echo Utils::formatDate($date->format('Y-m-d'));
                                    ?>
                                </p>
                            </div>
                            <button class="button gray only-icon no-border">
                                <i data-lucide="pen-line"></i>
                            </button>
                        </div>

                    <?php } else { ?>
                        <div class="card-option">
                            <div>
                                <p class="flex gap-1">Sans option</p>
                            </div>
                            <button class="link pro">
                                Ajouter une option
                            </button>
                        </div>
                    <?php } ?>

                </div>

                <div class="flex flex-col gap-2">
                    <a href="/offres/<?php echo $offer->id ?>/modification"
                       class="button purple fit mb-2" title="Avis non lu">
                        <!-- <i data-lucide="pen"></i>-->
                        Modifier
                    </a>
                    <a href="/dashboard/avis" class="button purple fit"
                            title="Avis non lu">
                        <i data-lucide="message-square-dot"></i>
                        <?php echo rand(3, 10) ?>
                    </a>
                    <a href="/dashboard/avis" class="button gray fit"
                       title="Avis non répondu">
                        <i data-lucide="message-square-more"></i>
                        <?php echo rand(2, 5) ?>
                    </a>
                    <a href="/dashboard/avis" class="button gray fit"
                       title="Avis blacklisté">
                        <i data-lucide="ban"></i>
                        <?php echo rand(1, 3) ?>
                    </a>
                </div>
            </article>
        <?php } ?>
    </div>
</div>