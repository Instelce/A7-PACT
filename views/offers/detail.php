<?php
/** @var $model \app\models\offer\Offer */
/** @var $offerTags \app\models\offer\OfferTag[] */
/** @var $this \app\core\View */

use app\core\form\Form;

$this->title = "Détails d'une offre";
$this->jsFile = "detailedOffer";

// echo "<pre>";
// var_dump($pk);
var_dump($offerData);
// echo "</pre>";

?>

<!---- Publication date ---->

<div class="publication">
    <p>Paru le </p>
    <p><?php echo $offerData["date"] ?></p>
</div>

<!---- Carousel ---->
<div class="paddingOfferDetailed">
    <x-carousel>
        <?php
        foreach ($offerData["url_images"] as $url) {
            ?><img slot="image" src="<?php echo $url ?>" alt="photo offre"><?php

        }
        ?>
        <!-- <img slot="image" src="/assets/images/exemples/brehat.jpeg" alt="img1"> -->
    </x-carousel>


    <!---- Infos ---->
    <h2 class="heading-2"><?php echo $offerData["title"] ?></h2>


    <div class="inlineOffer">
        <div class="inlineOfferGap">
            <p class="author">Par <?php echo $offerData["author"] ?></p>

            <p><?php echo $offerData["category"] ?></p>
            <div class="inlineOffer">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-map-pin">
                    <path
                        d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0" />
                    <circle cx="12" cy="10" r="3" />
                </svg>
                <p><?php echo $offerData["location"] ?></p>
            </div>

            <?php if (in_array($offerData["category"], ["Activité", "Visite", "Spectacle"])): ?>
                <div class="inlineOffer">
                    <p>Durée</p>
                    <p><?php echo $offerData["duration"] ?></p>
                    <p>H</p>
                </div>
            <?php endif; ?>


            <?php if (in_array($offerData["category"], ["Activité", "Parc d'attraction"])): ?>
                <div class="inlineOffer">
                    <p>A partir de </p>
                    <p><?php echo $offerData["required_age"] ?></p>
                    <p>ans</p>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <div>
        <h2 class="heading-2">Résumé :</h2>
        <br>
        <p><?php echo $offerData["summary"] ?></p>
    </div>

    <div class="columnOffer">

        <div class="inlineOffer">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-map-pin">
                <path
                    d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0" />
                <circle cx="12" cy="10" r="3" />
            </svg>
            <p><?php echo $offerData["address"] ?></p>
        </div>

        <?php if ($offerData["category"] !== "Restaurant"): ?>
            <div class="inlineOffer">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"
                     class="lucide lucide-coins">
                    <circle cx="8" cy="8" r="6" />
                    <path d="M18.09 10.37A6 6 0 1 1 10.34 18" />
                    <path d="M7 6h1v4" />
                    <path d="m16.71 13.88.7.71-2.82 2.82" />
                </svg>
                <p>
                    <?php if ($offerData["price"] == 0): ?>
                        Gratuit
                    <?php else: ?>
                        À partir de <?php echo $offerData["price"]; ?> € / personne
                    <?php endif; ?>
                </p>
            </div>
        <?php elseif ($offerData["category"] === "Restaurant"): ?>
            <div class="inlineOffer">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"
                     class="lucide lucide-coins">
                    <circle cx="8" cy="8" r="6" />
                    <path d="M18.09 10.37A6 6 0 1 1 10.34 18" />
                    <path d="M7 6h1v4" />
                    <path d="m16.71 13.88.7.71-2.82 2.82" />
                </svg>
                <p>
                    <?php
                    if (isset($offerData['minimum_price']) && isset($offerData['maximum_price'])) {

                        for ($i = 0; $i < $offerData['minimum_price']; $i++) {
                            echo "€";
                        }
                        echo " - ";
                        for ($i = 0; $i < $offerData['maximum_price']; $i++) {
                            echo "€";
                        }
                    } else {
                        echo "Prix non disponible";
                    }
                    ?>
                </p>
            </div>
        <?php endif; ?>







        <div class="inlineOffer">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-clock">
                <circle cx="12" cy="12" r="10" />
                <polyline points="12 6 12 12 16 14" />
            </svg>
            <p><?php echo $offerData["openValue"] ?></p>
        </div>

        <div class="inlineOffer">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-globe">
                <circle cx="12" cy="12" r="10" />
                <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20" />
                <path d="M2 12h20" />
            </svg>
            <p><a href="<?php echo $offerData["website"] ?>" target="_blank">Voir le site</a></p>
        </div>

        <div class="inlineOffer">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-phone">
                <path
                    d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
            </svg>
            <p><?php echo $offerData["phone_number"] ?></p>
        </div>

        <?php if ($offerData["category"] === "Visite"): ?>
            <div class="inlineOffer">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"
                     class="lucide lucide-languages">
                    <path d="m5 8 6 6" />
                    <path d="m4 14 6-6 2-3" />
                    <path d="M2 5h12" />
                    <path d="M7 2h1" />
                    <path d="m22 22-5-10-5 10" />
                    <path d="M14 18h6" />
                </svg>
                <p><?php echo $offerData["languages"] ?></p>
            </div>
        <?php endif; ?>
    </div>

    <div>
        <h2 class="heading-2">Description :</h2>
        <br>
        <p><?php echo $offerData["description"] ?></p>
    </div>

    <div class="inlineOffer">
        <h2 class="heading-2">Tags : </h2>
        <p class="heading-3"><?php echo $offerData["tags"] ?></p>
    </div>


    <div class="acordeonSize">
        <x-acordeon text="Grille tarifaire">
            <div slot="content">
                <p>Adhérent enfant : 0 € <br>
                    Adhérent adulte : 2 € <br>
                    Non adhérent enfant : 10 € <br>
                    Non adhérent adulte : 15 €
                </p>
            </div>
        </x-acordeon>

        <x-acordeon text="Prestations incluses">
            <div slot="content">
                <p>Encadrant <br>
                    Kit de crevaison <br>
                    Déjeuner et sandwich
                </p>
            </div>
        </x-acordeon>

        <x-acordeon text="Prestations non incluses">
            <div slot="content">
                <p>Bicyclette <br>
                    Crème solaire
                </p>
            </div>
        </x-acordeon>

        <x-acordeon text="Accessibilité">
            <div slot="content">
                <p>Le public en situation de handicap est le bienvenu, ne pas hésiter à nous appeler pour préparer la
                    balade
                </p>
            </div>
        </x-acordeon>
    </div>



</div>