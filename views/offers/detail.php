<?php
/** @var $pk int */
/** @var $model Offer */
/** @var $opinion Opinion */
/** @var $opinionSubmitted bool */
/** @var $userOpinion Opinion */
/** @var $offerTags OfferTag[] */

/** @var $this View */

use app\core\Application;
use app\core\form\Form;
use app\core\Utils;
use app\core\View;
use app\models\offer\Offer;
use app\models\offer\OfferTag;
use app\models\opinion\Opinion;

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

<input id="offer-id" type="hidden" value="<?php echo $pk ?>">

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
                <a href="/comptes/" class="underline"><?php echo $offerData["author"] ?></a>
            </p>
        </header>

        <?php echo $opinionExit ?>

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
                             src="<?php echo $offerData["cartePark"] ?>"
                             alt="Carte du Park">
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

            <input type="hidden" id="opinion-submitted" value="<?php echo $opinionSubmitted ?>">

            <?php if (!Application::$app->user?->isProfessional() && !$userOpinion) { ?>
                <button class="button gray spaced w-full" id="opinion-add">
                    Rédiger un avis
                    <i data-lucide="pen-line"></i>
                </button>
            <?php } ?>

            <!-- Creation form -->
            <?php if (Application::$app->isAuthenticated()) { ?>
                <?php $opinionForm = Form::begin('', "post", "opinion-form", "flex flex-col gap-2", 'multipart/form-data'); ?>

                <!-- Form name (used in controller) -->
                <input type="hidden" name="form-name" value="create-opinion">

                <!-- Rating choice -->
                <div class="flex flex-col gap-2 mb-2">
                    <label for="">Quelle note donneriez-vous à votre expérience ?</label>
                    <div class="rating-choice">
                        <input type="hidden" name="rating" value="<?php echo $opinion->rating ?>">
                    </div>
                    <?php echo $opinionForm->error($opinion, 'rating') ?>
                </div>

                <?php echo $opinionForm->field($opinion, 'visit_date')->dateField() ?>

                <!-- Visit context -->
                <div class="flex flex-col gap-2 mb-2">
                    <x-select id="opinion-context" name="visit_context" value="<?php echo $opinion->visit_context ?>" required>
                        <label slot="label">Qui vous accompagnait ?</label>
                        <span slot="trigger">Choisir une option</span>
                        <div slot="options">
                            <div data-value="affaires">Affaires</div>
                            <div data-value="couple">Couple</div>
                            <div data-value="famille">Famille</div>
                            <div data-value="amis">Amis</div>
                            <div data-value="solo">Solo</div>
                        </div>
                    </x-select>

                    <?php echo $opinionForm->error($opinion, 'visit_context') ?>
                </div>

                <?php echo $opinionForm->field($opinion, 'title') ?>
                <?php echo $opinionForm->textarea($opinion, 'comment') ?>

                <div>
                    <h3 class="mb-2">Ajoutez des photos <span class="text-sm text-gray-4">(facultatif)</span>
                    </h3>

                    <div class="flex flex-col gap-4">
                        <!-- Name for FILES -->
                        <div id="input-name" data-name="opinion-photos"></div>

                        <!-- Uploader -->
                        <label for="photo-input" class="image-uploader">
                            <input type="file" accept="image/png, image/jpeg" name="images[]"
                                   id="photo-input" multiple hidden>

                            <i data-lucide="upload"></i>
                            <p>Faire glisser des fichiers pour les uploader</p>
                            <span class="button gray">Selectionner les fichier à uploader</span>
                        </label>

                        <!-- Photos -->
                        <div id="photos" class="flex flex-col gap-2">
                            <div class="drag-line hidden"></div>
                        </div>

                    </div>
                </div>

                <div class="flex items-center gap-2 mt-2 mb-2">
                    <input class="checkbox checkbox-normal" type="checkbox"
                           id="opinion-certification">
                    <label for="opinion-certification" class="max-w-[400px]">Vous certifiez que
                        votre
                        Avis reflète
                        votre propre expérience et votre opinion sur cette Offre</label>
                </div>

                <!-- Form buttons -->
                <div class="flex gap-4 mt-2">
                    <button id="opinion-submit" type="submit" class="button gray w-full">
                        Publier
                        <i data-lucide="send"></i>
                    </button>

                    <button type="button" class="button gray" id="opinion-remove">
                        Annuler
                    </button>
                </div>

                <?php Form::end() ?>
            <?php } else { ?>
                <!-- Sign in reminder if not connected -->
                <div id="opinion-form" class="flex flex-col gap-4 hidden">
                    <p>Connectez-vous pour laisser un avis.</p>
                    <a href="/connexion" class="button sm">
                        Se connecter
                    </a>
                </div>
            <?php } ?>

            <!-- All opinions, generated in js file -->
            <div class="opinions-container">

                <!-- If the current user has already created an opinion -->
                <?php if ($userOpinion) { ?>
                    <div>
                        <p class="text-sm ml-5 mb-1">Votre avis</p>
                        <article class="opinion-card user-own">
                            <header>
                                <div>
                                    <a class="avatar" href="/comptes/<?php echo Application::$app->user->account_id ?>">
                                        <div class="image-container">
                                            <img src="<?php echo Application::$app->user->avatar_url ?>"
                                                 alt="<?php echo Application::$app->user->mail ?>">
                                        </div>
                                    </a>
                                    <a href="/comptes/<?php echo Application::$app->user->account_id ?>" class="user-name"><?php echo Application::$app->user->specific()->pseudo ?></a>
                                    <p class="text-sm text-gray-4">Créer le <?php echo Utils::formatDate($userOpinion->created_at) ?></p>
                                </div>
                                
                                <div class="buttons">

                                    <!-- Delete button -->
                                    <form method="post">
                                        <input type="hidden" name="form-name" value="delete-opinion">
                                        <input type="hidden" name="opinion_id" value="<?php echo $userOpinion->id ?>">
                                        <button class="button danger only-icon" title="Supprimer votre avis">
                                            <i data-lucide="trash" stroke-width="2"></i>
                                        </button>
                                    </form>

                                </div>
                            </header>

                            <!-- Stars -->
                            <div class="opinion-card-stars">
                                <p class="text-sm text-gray-4">A noté</p>
                                <div class="stars" data-number="<?php echo $userOpinion->rating ?>"></div>
                            </div>

                            <div class="flex flex-col gap-1 mb-4">
                                <!-- Title -->
                                <h3 class="heading-2 font-title"><?php echo $userOpinion->title ?></h3>

                                <!-- Comment -->
                                <p><?php echo $userOpinion->comment ?></p>
                            </div>

                            <div class="opinion-card-photos">
                                <?php foreach ($userOpinion->photos() as $photo) { ?>
                                    <img src="<?php echo $photo->photo_url ?>" alt="<?php echo $userOpinion->title ?>">
                                <?php } ?>
                            </div>
                        </article>
                    </div>
                <?php } ?>

                <button id="loader-button" class="button gray">Charger plus d'avis</button>
            </div>
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
            <a href="/offres/<?php echo $pk ?>/modification" class="button purple">
                Modifier l'offre
            </a>
        <?php } ?>
    </aside>
</div>