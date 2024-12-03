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

    <div class="flex flex-col">
        <!-- Offer cards -->
        <?php foreach ($offers as $i => $offer) {
            $type = $offersType[$i]->type;
            $subscription = $offersSubscription[$i];
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

                    <p class="mt-3"><?php echo $offer->summary ?></p>

                    <div class="flex flex-col gap-2 mt-4">
                        <p class="text-gray-4 flex items-center gap-2">
                            <?php echo Offer::frenchCategoryName($offer->category) ?>
                            <span class="dot"></span> <?php echo $price ?>
<!--                            <span class="dot"></span> --><?php //echo $offer->likes . ' likes' ?>
                            <span class="dot"></span> <?php echo $offer->opinionsCount() ?> avis
                        </p>
                        <p class="text-gray-4">Mis à jour
                            le <?php echo Utils::formatDate($offer->updated_at); ?></p>
                    </div>

                    <!-- Option -->
                    <?php if ($subscription) { ?>
                        <div class="card-option">
                            <div>
                                <p class="flex gap-1">Avec l'option <span
                                        class="underline"><?php echo Utils::formatTypeString($subscription->type()) ?></span>
                                </p>
                                <p class="text-gray-4">
                                    Du <?php echo Utils::formatDate($subscription->launch_date); ?>
                                    au <?php echo Utils::formatDate($subscription->endDate()); ?>
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
                            <button class="link pro" id="add-option-button">
                                Ajouter une option
                            </button>
                        </div>

                        <!-- Form to add an option to the offer -->
                        <form method="post" id="add-option-form" class="flex flex-col gap-2 mt-6 hidden">
                            <input type="hidden" name="form-name" value="add-option">
                            <input type="hidden" name="offer_id" value="<?php echo $offer->id ?>">

                            <!-- Option type -->
                            <div class="option-choices grid grid-cols-2 gap-2">
                                <label for="option-relief" class="button gray">
                                    En relief
                                    <input id="option-relief" type="radio" name="type" value="en_relief" checked>
                                </label>
                                <label for="option-a-la-une" class="button gray">
                                    A la une
                                    <input id="option-a-la-une" type="radio" name="type" value="a_la_une">
                                </label>
                            </div>

                            <!-- Other fields, launch date and week number -->
                            <div id="option-dates" class="flex gap-4 mt-2 w-full">
                                <x-input>
                                    <p slot="label">Date de lancement</p>
                                    <input slot="input" type="date" step="7" name="launch_date" value="<?php echo date('Y-m-d', strtotime("next Monday ")) ?>" min="<?php echo date('Y-m-d', strtotime("next Monday")) ?>">
                                    <p slot="helper">L'option prendra effet en début de semaine</p>
                                </x-input>
                                <x-input>
                                    <p slot="label">Nombre de semaine</p>
                                    <input slot="input" type="number" name="duration" max="4"
                                           min="1" value="1">
                                </x-input>
                            </div>

                            <!-- Form buttons -->
                            <div class="flex gap-2 mt-2">
                                <button class="button purple w-full">Ajouter</button>
                                <button type="button" class="button gray" id="close-option-form">Annuler</button>
                            </div>
                        </form>
                    <?php } ?>

                </div>

                <div class="flex flex-col gap-2">
                    <a href="/offres/<?php echo $offer->id ?>/modification"
                       class="button purple fit mb-2" title="Avis non lu">
                        <!-- <i data-lucide="pen"></i>-->
                        Modifier
                    </a>
                    <a href="/dashboard/avis?filter=non-lu" class="button purple fit"
                            title="Avis non lu">
                        <i data-lucide="message-square-dot"></i>
                        <?php echo $offer->noReadOpinions() ?>
                    </a>
                    <a href="/dashboard/avis" class="button gray fit"
                       title="Avis non répondu">
                        <i data-lucide="message-square-more"></i>
                        <?php echo $offer->opinionsCount() ?>
                    </a>
                    <a href="/dashboard/avis" class="button gray fit"
                       title="Avis blacklisté">
                        <i data-lucide="ban"></i>
                        <?php echo 0 ?>
                    </a>
                </div>
            </article>
        <?php } ?>
    </div>
</div>