<?php
/** @var $model \app\models\offer\Offer */
/** @var $offerTags \app\models\offer\OfferTag[] */
/** @var $this \app\core\View */

use app\core\form\Form;

$this->title = "Détails d'une offre";
$this->jsFile = "detailedOffer";

// echo "<pre>";
// var_dump($pk);
//Svar_dump($offerData);
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
    <h2 class="heading-1"><?php echo $offerData["title"] ?></h2> <!-- title -->

    <div class="inlineOfferTop">

        <div class="inlineOfferGap">
            <p class="author">Par <?php echo $offerData["author"] ?></p> <!-- athor -->
            <p><?php echo $offerData["category"] ?></p> <!-- category -->
            <div class="inlineOffer">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"
                     class="lucide lucide-map-pin">
                    <path
                        d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/>
                    <circle cx="12" cy="10" r="3"/>
                </svg>
                <p><?php echo $offerData["location"] ?></p> <!-- location -->
            </div>

            <?php if (in_array($offerData["category"], ["Activité", "Visite", "Spectacle"])): ?>
                <div class="inlineOffer">
                    <p>Durée</p>
                    <p><?php echo $offerData["duration"] ?></p> <!-- duration -->
                    <p>H</p>
                </div>
            <?php endif; ?>

            <?php if (in_array($offerData["category"], ["Activité", "Parc d'attraction"])): ?>
                <div class="inlineOffer">
                    <p>A partir de </p>
                    <p><?php echo $offerData["required_age"] ?></p> <!-- required age -->
                    <p>ans</p>
                </div>
            <?php endif; ?>

        </div>

        <div class="inlineOfferGap">
            <?php if ($offerData["category"] !== "Restaurant"): ?>
                <div class="inlineOffer">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"
                         class="lucide lucide-coins">
                        <circle cx="8" cy="8" r="6"/>
                        <path d="M18.09 10.37A6 6 0 1 1 10.34 18"/>
                        <path d="M7 6h1v4"/>
                        <path d="m16.71 13.88.7.71-2.82 2.82"/>
                    </svg>
                    <p>
                        <?php if ($offerData["price"] == 0): ?> <!-- price -->
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
                        <circle cx="8" cy="8" r="6"/>
                        <path d="M18.09 10.37A6 6 0 1 1 10.34 18"/>
                        <path d="M7 6h1v4"/>
                        <path d="m16.71 13.88.7.71-2.82 2.82"/>
                    </svg>
                    <p> <!-- price if Restaurant-->
                        <?php
                        for ($i = 0; $i < $offerData['range_price']; $i++) {
                            echo "€";
                        }
                        ?>
                    </p>
                </div>
            <?php endif; ?>

            <div class="inlineOffer">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"
                     class="lucide lucide-clock">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg> <!-- Status of the offer -->
                <?php
                $status = $offerData["status"];
                $class = "";

                if ($status == "Fermé") {
                    $class = "closed";
                } elseif ($status == "Ferme bientôt") {
                    $class = "closing-soon";
                } elseif ($status == "Ouvert") {
                    $class = "open";
                }
                ?>

                <p class="<?php echo $class; ?>"><?php echo $status; ?></p>
            </div>

        </div>
    </div>

    <div>
        <h2 class="heading-2">Résumé :</h2>
        <br>
        <p><?php echo $offerData["summary"] ?></p> <!-- Summary -->
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
            <p><?php echo $offerData["address"] ?></p> <!-- address of the offer-->
        </div>




        <div class="inlineOffer">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-globe">
                <circle cx="12" cy="12" r="10" />
                <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20" />
                <path d="M2 12h20" />
            </svg>
            <p><a href="<?php echo $offerData["website"] ?>" target="_blank">Voir le site</a></p> <!-- link to the website -->
        </div>

        <div class="inlineOffer">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-phone">
                <path
                    d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
            </svg>
            <p><?php echo $offerData["phone_number"] ?></p> <!-- phone number of the creator of the offer -->
        </div>

        <?php if ($offerData["category"] === "Visite"): ?> <!-- languages of the visit -->
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
        <p><?php echo $offerData["description"] ?></p> <!-- description of the offer-->
    </div>

    <div class="containerAcordeon">
        <h2 class="heading-2">Tags : </h2> <!-- tags associated with the offer -->
        <?php
            $nbTags = count($offerData['tags']);
            $compteurTag = 0;
            if (!empty($offerData['tags'])):
            foreach ($offerData['tags'] as $tag):
                $compteurTag++; ?>
                <p><?php echo $tag ;?>
                <?php if($nbTags > $compteurTag){?>
                    ,</p>
                <?php }
                else {?>
                    </p>
                <?php } ?>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucun tag associé à cette offre.</p>
        <?php endif; ?>





    <div class="acordeonSize">
        <!--- <x-acordeon text="Grille tarifaire">
            <div slot="content">
                <p>Adhérent enfant : 0 € <br>
                    Adhérent adulte : 2 € <br>
                    Non adhérent enfant : 10 € <br>
                    Non adhérent adulte : 15 €
                </p>
            </div>
        </x-acordeon> --->

        <?php if (!empty($offerData["prestationsIncluses"])): ?> <!-- Included and not included prestations of the offer. They are displayed only if somethinf is write in them -->
            <x-acordeon text="Prestations incluses">
                <div slot="content">
                    <p><?php echo $offerData["prestationsIncluses"] ?></p>
                </div>
            </x-acordeon>
        <?php endif; ?>

        <?php if (!empty($offerData["prestationsNonIncluses"])): ?>
            <x-acordeon text="Prestations non incluses">
                <div slot="content">
                    <p><?php echo $offerData["prestationsNonIncluses"] ?></p>
                </div>
            </x-acordeon>
        <?php endif; ?>

        <?php if (!empty($offerData["accessibilite"])): ?>
            <x-acordeon text="Accessibilité">
                <div slot="content">
                    <p><?php echo $offerData["accessibilite"] ?></p>
                </div>
            </x-acordeon>
        <?php endif; ?>

    </div>



</div>