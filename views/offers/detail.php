<?php
/** @var $model Offer */
/** @var $offerTags OfferTag[] */

/** @var $this View */

use app\core\Application;
use app\core\form\Form;
use app\core\View;
use app\models\offer\Offer;
use app\models\offer\OfferTag;

$this->title = $offerData["title"];
$this->jsFile = "offerDetail";
$this->cssFile = "offers/detail";
$this->waves = true;
$this->leaflet = true;

// Get the class in function of the status
$status = $offerData["status"];
$class = "";

if ($status == "Fermé") {
    $class = "offer-closed";
} elseif ($status == "Ferme bientôt") {
    $class = "offer-closing-soon";
} elseif ($status == "Ouvert") {
    $class = "offer-open";
}

?>


<!-- Publication date -->
<!--<div class="publication">-->
<!--    <p>Paru le </p>-->
<!--    <p>--><?php //echo \app\core\Utils::formatDate($offerData["date"]) ?><!--</p>-->
<!--</div>-->

<div class="lg:grid grid-cols-6 gap-8 md:flex">

    <!-- Main container -->
    <div class="w-full flex flex-col col-span-4">
        <!-- Carousel -->
        <x-carousel>
            <?php foreach ($offerData["url_images"] as $url) { ?>
                <img slot="image" src="<?php echo $url ?>" alt="photo offre">
            <?php } ?>
        </x-carousel>

        <!-- Header -->
        <header class="page-header">
            <h2 class="heading-2 font-title"><?php echo $offerData["title"] ?></h2> <!-- title -->

            <p>
                <?php echo $offerData["category"] ?>
                par
                <a href="/comptes/" class="underline"><?php echo $offerData["author"] ?></a></p>
        </header>

        <!-- Tags -->
        <div class="flex gap-2 mb-8">
            <?php foreach ($offerData['tags'] as $i => $tag): ?>
                <a href="/recherche?tags=<?php echo $tag; ?>" class="badge random">
                    <?php echo ucfirst($tag); ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Summary and description -->
        <div class="flex flex-col gap-4 mb-8">
            <p><?php echo $offerData["summary"] ?></p>
            <p><?php echo $offerData["description"] ?></p>
        </div>

        <!-- Information presented in a list with icons -->
        <div class="flex flex-col gap-3 mb-8">

            <!-- Duration only for activities, visits and shows -->
            <?php if (in_array($offerData["category"], ["Activité", "Visite", "Spectacle"])): ?>
                <div class="inline-offer">
                    <i data-lucide="timer"></i>
                    <p>Durée <?php echo $offerData["duration"] ?>h</p>
                </div>
            <?php endif; ?>

            <!-- Required age only for activities and attraction parks -->
            <?php if (in_array($offerData["category"], ["Activité", "Parc d'attraction"])): ?>
                <div class="inline-offer">
                    <i data-lucide="circle-slash"></i>
                    <p>A partir de <?php echo $offerData["required_age"] ?> ans</p>
                </div>
            <?php endif; ?>

            <!-- Price -->
            <?php if ($offerData["category"] !== "Restaurant"): ?>
                <div class="inline-offer">
                    <i data-lucide="coins"></i>

                    <p>
                        <?php if ($offerData["price"] == 0): ?>
                            Gratuit
                        <?php else: ?>
                            À partir de <?php echo $offerData["price"]; ?> € / personne
                        <?php endif; ?>
                    </p>
                </div>

                <!-- Price for restaurant -->
            <?php elseif ($offerData["category"] === "Restaurant"): ?>
                <div class="inline-offer">
                    <i data-lucide="coins"></i>

                    <p>
                        <?php for ($i = 0; $i < $offerData['range_price']; $i++) {
                            echo "€";
                        } ?>
                    </p>
                </div>
            <?php endif; ?>

            <div class="inline-offer">
                <i data-lucide="clock"></i>

                <p class="<?php echo $class; ?>"><?php echo $status; ?></p>
            </div>

            <div class="inline-offer">
                <i data-lucide="map-pin"></i>

                <p><?php echo $offerData["address"] ?></p> <!-- address of the offer-->
            </div>

            <div class="inline-offer">
                <i data-lucide="globe"></i>

                <p><a href="<?php echo $offerData["website"] ?>" target="_blank">Voir le site</a>
                </p>
                <!-- link to the website -->
            </div>

            <div class="inline-offer">
                <i data-lucide="phone"></i>

                <p><?php echo $offerData["phone_number"] ?></p>
                <!-- phone number of the creator of the offer -->
            </div>

            <?php if ($offerData["category"] === "Visite"): ?> <!-- languages of the visit -->
                <div class="inline-offer">
                    <i data-lucide="languages"></i>

                    <p><?php echo $offerData["languages"] ?></p>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($offerData["category"] === "Restaurant"): ?>
            <div class="mb-8">
                <?php if (!empty($offerData["carteRestaurant"]) && filter_var($offerData["carteRestaurant"], FILTER_VALIDATE_URL)): ?>
                    <a href="<?php echo $offerData["carteRestaurant"] ?>" target="_blank">
                        <img class="rounded mb-2 max-w-64 max-h-64"
                             src="<?php echo $offerData["carteRestaurant"] ?>"
                             alt="Carte du Restaurant">
                    </a>
                <?php else: ?>
                    <p>Aucune carte disponible pour ce restaurant.</p>
                <?php endif; ?>
            </div>
        <?php elseif ($offerData["category"] === "Parc d'attraction"): ?>
            <div class="mb-8">
                <?php if (!empty($offerData["cartePark"]) && filter_var($offerData["cartePark"], FILTER_VALIDATE_URL)): ?>
                    <a href="<?php echo $offerData["cartePark"] ?>" target="_blank">
                        <img class="rounded mb-4 max-w-64 max-h-64"
                             src="<?php echo $offerData["cartePark"] ?>" alt="Carte du Park">
                    </a>
                <?php else: ?>
                    <p>Aucune carte disponible pour ce parc d'attraction.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>


        <div class="containerAcordeon">

            <div class="acordeonSize divide-y divide-gray-1">
                <!--- <x-acordeon text="Grille tarifaire">
                <div slot="content">
                    <p>Adhérent enfant : 0 € <br>
                        Adhérent adulte : 2 € <br>
                        Non adhérent enfant : 10 € <br>
                        Non adhérent adulte : 15 €
                    </p>
                </div>
            </x-acordeon> --->

                <?php if (!empty($offerData["prestationsIncluses"])): ?>
                    <x-acordeon text="Prestations incluses">
                        <div slot="content">
                            <p>
                                <?php echo $offerData["prestationsIncluses"] ?>
                            </p>
                        </div>
                    </x-acordeon>
                <?php endif; ?>

                <?php if (!empty($offerData["prestationsNonIncluses"])): ?>
                    <x-acordeon text="Prestations non incluses">
                        <div slot="content">
                            <p>
                                <?php echo $offerData["prestationsNonIncluses"] ?>
                            </p>
                        </div>
                    </x-acordeon>
                <?php endif; ?>

                <?php if (!empty($offerData["accessibilite"])): ?>
                    <x-acordeon text="Accessibilité">
                        <div slot="content">
                            <p>
                                <?php echo $offerData["accessibilite"] ?>
                            </p>
                        </div>
                    </x-acordeon>
                <?php endif; ?>

            </div>

        </div>


        <!-- ------------------------------------------------------------------- -->
        <!-- Avis                                                                -->
        <!-- ------------------------------------------------------------------- -->

        <section>
            <h2 class="section-header">Les avis</h2>

            <button class="button gray spaced w-full">
                Rédiger un avis
                <i data-lucide="pen-line"></i>
            </button>
        </section>
    </div>

    <!-- Sidebar -->
    <aside class="sticky col-span-2 h-fit flex flex-col gap-4 top-navbar-height">
        <div class="map-container">
            <div id="map" class="map"></div>
            <!-- <button class="button gray spaced">
                Itinéraire
                <i data-lucide="map"></i>
            </button>
            <button class="button gray spaced">
                Ouvrir dans Maps
                <i data-lucide="arrow-up-right"></i>
            </button> -->
        </div>

        <?php if (Application::$app->user?->isProfessional()) { ?>
            <a href="/offres/<?php echo $pk ?>/modification"
               class="button purple">
                Modifier l'offre
            </a>
        <?php } ?>
    </aside>
</div>