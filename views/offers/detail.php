<?php
/** @var $pk int */
/** @var $model Offer */
/** @var $opinion Opinion */
/** @var $opinionSubmitted bool */
/** @var $userOpinion Opinion */
/** @var $offerTags OfferTag[] */
/** @var $offer Offer */

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

$longitude = $offer->address()->longitude;
$latitude = $offer->address()->latitude;

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
            <div class="flex gap-3 items-center flex-wrap">
                <h2 class="heading-2 font-title"><?php echo $offerData["title"] ?></h2> <!-- title -->
            </div>

            <div class="flex gap-3 items-center flex-wrap">
                <p>
                    <?php echo $offerData["category"] ?>
                    par
                    <a href="/comptes/<?php echo $offerData["professionalId"] ?>"
                        class="underline"><?php echo $offerData["author"] ?></a>
                </p>
                <!-- <span class="dot"></span> -->
                <div class="flex gap-3 items-center">
                    <div class="stars" data-number="<?php echo $offerData["rating"] ?>"></div>
                    <p>(<?php echo $offer->opinionsCount() ?> avis)</p>
                </div>
            </div>
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

        <!-- Summary -->
        <div class="flex flex-col gap-4 mb-8">
            <p><?php echo $offerData["summary"] ?></p>
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

            <div class="lg:hidden sm: flex flex-col gap-3">
                <?php if (!empty($status)) { ?>
                    <div class="inline-offer">
                        <i data-lucide="clock"></i>
                        <p class="<?php echo $class; ?>"><?php echo $status; ?></p>
                    </div>
                <?php } ?>

                <div class="inline-offer">
                    <i data-lucide="coins"></i>
                    <p>
                        <?php echo $offerData["price"]; ?>
                    </p>
                </div>
            </div>


            <div class="inline-offer">
                <i data-lucide="map-pin"></i>

                <p><?php echo $offerData["address"] ?></p> <!-- address of the offer-->
            </div>

            <div class="inline-offer">
                <i data-lucide="globe"></i>

                <p><a href="<?php echo $offerData["website"] ?>" class="underline"
                        target="_blank"><?php echo $offerData["website"] ?></a>
                </p>
                <!-- link to the website -->
            </div>

            <div class="inline-offer">
                <i data-lucide="phone"></i>

                <p><?php echo $offerData["phone_number"] ?></p>
                <!-- phone number of the creator of the offer -->
            </div>

            <?php if ($offerData["category"] === "Visite"): ?>
                <!-- languages of the visit -->
                <div class="inline-offer">
                    <i data-lucide="languages"></i>

                    <p><?php echo $offerData["languages"] ?></p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Description -->
        <div class="flex flex-col gap-4 mb-8">
            <p><?php echo $offerData["description"] ?></p>
        </div>


        <?php if ($offerData["category"] === "Restaurant"): ?>
            <div class="mb-8">
                <?php if (!empty($offerData["carteRestaurant"]) && filter_var($offerData["carteRestaurant"], FILTER_VALIDATE_URL)): ?>
                    <a href="<?php echo $offerData["carteRestaurant"] ?>" target="_blank">
                        <img class="rounded mb-2 max-w-64 max-h-64" src="<?php echo $offerData["carteRestaurant"] ?>"
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
                        <img class="rounded mb-4 max-w-64 max-h-64" src="<?php echo $offerData["cartePark"] ?>"
                            alt="Carte du Park">
                    </a>
                <?php else: ?>
                    <p>Aucune carte disponible pour ce parc d'attraction.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>


        <div class="containerAcordeon">

            <div class="acordeonSize divide-y divide-gray-1">

                <?php if (!empty($offerData["openingHours"])): ?>
                    <x-acordeon text="Horaires d'ouverture">
                        <div slot="content">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th class="table-head" colspan="3">Grille des horaires</th>
                                    </tr>
                                    <tr>
                                        <th>Jour</th>
                                        <th>Ouverture</th>
                                        <th>Fermeture</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($offerData['openingHours'] as $openingHour) { ?>
                                        <tr>
                                            <td><?php echo $openingHour->frenchDay() ?></td>
                                            <td><?php echo $openingHour->opening_hours ?></td>
                                            <td><?php echo $openingHour->closing_hours ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
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
                <button class="button gray spaced w-full mb-8" id="opinion-add">
                    Rédiger un avis
                    <i data-lucide="pen-line"></i>
                </button>
            <?php } ?>

            <?php if (Application::$app->user?->isProfessional()) { ?>
                <p class="mb-6 block">En tant que professionnel vous ne pouvez pas rédiger d'avis.</p>
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
                    <x-select id="opinion-context" name="visit_context" value="<?php echo $opinion->visit_context ?>"
                        required>
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
                            <input type="file" accept="image/png, image/jpeg" name="images[]" id="photo-input" multiple
                                hidden>

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
                    <input class="checkbox checkbox-normal" type="checkbox" id="opinion-certification">
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
                <div id="opinion-form" class="flex flex-col gap-4 hidden mb-8">
                    <p>Connectez-vous pour répondre, réagir ou laisser un avis.</p>
                    <a href="/connexion" class="button sm">
                        Se connecter
                    </a>
                </div>
            <?php } ?>

            <!-- All opinions, generated in js file -->
            <div class="opinions-container">

                <!-- If the current user has already created an opinion -->
                <?php if ($userOpinion) { ?>
                    <div class="mb-4">
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
                                    <a href="/comptes/<?php echo Application::$app->user->account_id ?>"
                                        class="user-name"><?php echo Application::$app->user->specific()->pseudo ?></a>
                                    <p class="text-sm text-gray-4">Créer le
                                        <?php echo Utils::formatDate($userOpinion->created_at) ?>
                                    </p>
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


                            <!-- Photos -->
                            <?php if (count($userOpinion->photos()) > 0) { ?>
                                <div class="opinion-card-photos">
                                    <?php foreach ($userOpinion->photos() as $photo) { ?>
                                        <img src="<?php echo $photo->photo_url ?>" alt="<?php echo $userOpinion->title ?>">
                                    <?php } ?>
                                </div>
                            <?php } ?>


                        </article>
                    </div>
                <?php } ?>

                <button id="loader-button" class="button gray">Charger plus d'avis</button>
            </div>
        </section>
    </div>

    <!-- Sidebar -->
    <aside class="sticky col-span-2 h-fit flex flex-col gap-3 top-navbar-height">
        <div class="map-container">
            <!-- Generated with leaflet -->
            <div id="map" class="map"></div>
            <input type="hidden" id="map-latitude" value="<?php echo $latitude ?>">
            <input type="hidden" id="map-longitude" value="<?php echo $longitude ?>">
            <a href="https://www.google.com/maps/dir/?api=1&origin=Ma+Localisation&destination=<?php echo $latitude . ',' . $longitude ?>"
                class="button gray spaced">
                Itinéraire
                <i data-lucide="map"></i>
                </button>
                <a class="button gray spaced"
                    href="https://maps.google.com/?q=<?php echo $latitude . ',' . $longitude ?>" target="_blank">
                    Ouvrir dans Maps
                    <i data-lucide="arrow-up-right"></i>
                </a>
        </div>
        <div class="important_data">
            <div class="flex gap-4 items-center">
                <?php if (!empty($status)) { ?>
                    <div class="inline-offer">
                        <i data-lucide="clock"></i>

                        <p class="<?php echo $class; ?>"><?php echo $status; ?></p>
                    </div>
                <?php } ?>
                <div class="inline-offer">
                    <i data-lucide="coins"></i>
                    <p>
                        <?php echo $offerData["price"]; ?>
                    </p>
                </div>
            </div>
        </div>



        <?php if (Application::$app->user?->isProfessional() && Application::$app->user->specific()->hasOffer($pk)) { ?>
            <a href="/offres/<?php echo $pk ?>/modification" class="button purple">
                Modifier l'offre
            </a>
        <?php } ?>
    </aside>
</div>

<!-- Tchatator interface -->
<?php if (Application::$app->userType === 'member') { ?>
    <div class="chat-container">
        <button class="chat-trigger button gray only-icon">
            <i data-lucide="message-circle"></i>
        </button>

        <div class="chat">
            <!-- Generated in JS -->
            <div class="messages-container">
            </div>

            <label for="message-content" class="hidden">Message</label>
            <textarea id="message-content" class="message-content" cols="30" rows="5"></textarea>
            <button class="send-button">
                <i data-lucide="send"></i>
            </button>
        </div>
    </div>
<?php } ?>

