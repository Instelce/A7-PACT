<?php
/** @var $this \app\core\View */
/** @var $offers \app\models\offer\Offer[] */

use app\core\Application;
use app\core\Utils;

$this->title = "Factures";
$this->cssFile = "dashboard/factures";

$month = match (date("m")) {
    "01" => "Janvier",
    "02" => "Février",
    "03" => "Mars",
    "04" => "Avril",
    "05" => "Mai",
    "06" => "Juin",
    "07" => "Juillet",
    "08" => "Août",
    "09" => "Septembre",
    "10" => "Octobre",
    "11" => "Novembre",
    "12" => "Décembre",
    default => "Erreur",
};

?>

<div class="flex gap-8">

    <!-- Tabs button -->
    <div class="flex flex-col min-w-[250px] h-fit sticky top-navbar-height">
        <div class="pro-name">
            <h1><?php echo Application::$app->user->specific()->denomination ?></h1>
        </div>

        <a href="/dashboard/offres">
            <div class="tab-button pro">
                <i data-lucide="ticket"></i>
                Mes offres
            </div>
        </a>
        <a href="/dashboard/avis">
            <div class="tab-button pro">
                <i data-lucide="message-circle"></i>
                Avis reçus
            </div>
        </a>
        <div class="tab-button pro" selected>
            <i data-lucide="file-text"></i>
            Factures
        </div>

        <a href="/offres/creation" class="button gray icon-left mt-4">
            <i data-lucide="plus"></i>
            Créer une offre
        </a>
    </div>

    <div class="flex flex-col w-full">

        <!-- Card on top for small infos -->
        <div class="grid grid-cols-2 gap-4 w-full mb-8">
            <div class="card-info">
                <p>Coût actuel pour <?php echo $month ?></p>
                <h2>54 €</h2>
            </div>
            <div class="card-info">
                <p>Jour en ligne toutes offres confondu</p>
                <h2>39</h2>
            </div>
        </div>

        <!-- Options -->
        <div>
            <h2 class="section-header">Options en cours</h2>

            <div class="flex flex-col gap-4">
                <?php foreach ($offers as $offer) {
                    $subscription = $offer->subscription();
                ?>
                    <?php if ($subscription) {
                        $option = $subscription->option();
                    ?>
                        <article>
                            <h3 class="font-title text-lg mb-2">Option <span class="font-bold"><?php echo $option->french() ?></span> pour <span class="underline"><?php echo $offer->title ?></span></h3>

                            <div class="flex justify-between">
                                <div class="flex flex-col gap-0.5">
                                    <p>Du <?php echo Utils::formatDate($subscription->launch_date) ?> au <?php echo Utils::formatDate($subscription->endDate()) ?></p>
                                    <p>Active pour <?php echo $subscription->duration ?> semaines</p>
                                    <p>Active depuis <?php echo $subscription->durationInDays() ?> jours</p>
                                </div>

                                <div class="flex flex-col">
                                    <span>Vous revient à</span>
                                    <span class="heading-1 font-normal font-title"><?php echo $option->price * $subscription->duration ?> €</span>
                                </div>
                            </div>
                        </article>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>


        <!-- Factures -->
        <div>
            <h2 class="section-header">Options en cours</h2>

        </div>
    </div>
</div>