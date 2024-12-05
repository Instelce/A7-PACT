<?php
/** @var $this \app\core\View */
/** @var $offers \app\models\offer\Offer[] */
/** @var $invoices \app\models\payment\Invoice[] */

/** @var $subscriptions \app\models\offer\Subscription[] */

use app\core\Application;
use app\core\Utils;

$this->title = "Factures";
$this->cssFile = "dashboard/factures";
$this->jsFile = "dashboard/factures";

// This array contains the months of the invoices as KEYS and invoice and offer as VALUES
$invoiceMonths = [];

foreach ($invoices as $i => $invoice) {
    $invoiceMonths[$invoice->service_date][] = [$invoice, $invoice->offer()];
}

$monthTotal = 0;
$activeDaysTotal = 0;
foreach ($offers as $offer) {
    $offer->activeDaysToNow();
    $monthTotal += $offer->type()->price * $offer->activeDaysToNow();
    $activeDaysTotal += $offer->activeDaysToNow();
}

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
                <p>Coût actuel pour <?php echo Utils::monthConversion(date("m")) ?></p>
                <h2><?php echo $monthTotal ?> € <span class="text-sm">HT</span></h2>
            </div>
            <div class="card-info">
                <p>Jour en ligne toutes offres confondu</p>
                <h2><?php echo $activeDaysTotal ?></h2>
            </div>
        </div>

        <!-- Options -->
        <div>
            <h2 class="section-header">Options en cours</h2>

            <div class="flex flex-col gap-4">
                <?php foreach ($subscriptions as $subscription) {
                    $option = $subscription->option();
                    ?>
                    <article class="option-card">
                        <div>
                            <h3 class="text-lg mb-2">Option <span
                                    class="font-bold"><?php echo $option->french() ?></span> pour
                                <span
                                    class="underline"><?php echo $subscription->offer()->title ?></span>
                            </h3>

                            <div class="flex flex-col gap-0.5">
                                <p class="flex gap-2 items-center">
                                    Du <?php echo Utils::formatDateWithSlash($subscription->launch_date) ?>
                                    au <?php echo Utils::formatDateWithSlash($subscription->endDate()) ?>
                                    <span class="dot"></span> <?php echo $subscription->duration ?>
                                    semaines</p>
                                <?php if ($subscription->isActive()) { ?>
                                    <p>Active depuis <?php echo $subscription->durationInDays() ?>
                                        jours</p>
                                <?php } else { ?>
                                    <p>Active dans <?php echo $subscription->startDurationDays() ?>
                                        jours</p>
                                <?php } ?>
                            </div>
                        </div>

                        <!-- Option price -->
                        <div class="flex flex-col text-right">
                            <!--<span>Vous revient à</span>-->
                            <span
                                class="heading-1 font-normal font-title"><?php echo $option->price * $subscription->duration ?> € <span
                                    class="text-sm">HT</span></span>
                            <span class="text-gray-4">pour le <?php echo date("d/m", strtotime($subscription->endDate())) ?></span>
                        </div>
                    </article>
                <?php } ?>

                <?php if (empty($subscriptions)) { ?>
                    <p>Aucune option souscrite.</p>
                <?php } ?>
            </div>
        </div>


        <!-- Factures -->
        <div class="mt-8">
            <h2 class="section-header">Vos factures</h2>

            <div class="flex flex-col gap-2">
                <?php foreach ($invoiceMonths as $month => $couple) { ?>
                    <div class="accordion">
                        <div class="accordion-trigger">
                            <h3><?php echo Utils::monthConversion($month) . " " . date("Y", strtotime($couple[0][0]->issue_date)) ?></h3>
                            <i data-lucide="chevron-down" class="w-[18px] h-[18px]"></i>
                        </div>
                        <div class="accordion-content flex flex-col gap-4">
                            <?php foreach ($couple as $data) {
                                /** @var \app\models\payment\Invoice $invoice */
                                /** @var \app\models\offer\Offer $offer */
                                [$invoice, $offer] = $data;
                                $activeDays = $invoice->activeDays();
                                $badgeColor = match ($offer->type()->type) {
                                    "premium" => "yellow",
                                    "standard" => "blue",
                                    default => "gray",
                                };
                                ?>
                                <article class="invoice-card">
                                    <div>
                                        <header>
                                            <h3><?php echo $offer->title ?></h3>
                                            <span
                                                class="badge <?php echo $badgeColor ?>"><?php echo ucfirst($offer->type()->type) ?></span>
                                        </header>

                                        <div class="card-histories hidden">
                                            <div class="week-days">
                                                <span>Lun</span>
                                                <span>Mar</span>
                                                <span>Mer</span>
                                                <span>Jeu</span>
                                                <span>Ven</span>
                                                <span>Sam</span>
                                                <span>Dim</span>
                                            </div>
                                            <div class="calendar">
                                                <!-- Offset of the last month day -->
                                                <?php for ($i = 1; $i < date('N', strtotime($invoice->issue_date)); $i++) { ?>
                                                    <span class="last-month"></span>
                                                <?php } ?>
                                                <!-- Day of the current month -->
                                                <?php foreach ($invoice->histories() as $day => $status) { ?>
                                                    <span class="<?php echo $status === "offline" ? "" : "active" ?>">
                                                        <?php echo $day ?>
                                                    </span>
                                                <?php } ?>
                                            </div>
                                        </div>

                                        <div class="card-buttons">
                                            <button id="toggle-histories" class="button gray sm" title="Voir l'historique du status de l'offre">
                                                Voir l'historique
                                            </button>

                                            <a href="/factures/<?php echo $invoice->id ?>?download=true"
                                               class="button gray sm" target="_blank" title="Télécharger la facture">
                                                Télécharger
                                                <i data-lucide="download"></i>
                                            </a>

                                            <a href="/factures/<?php echo $invoice->id ?>"
                                               class="button gray sm" target="_blank" title="Visualizer la facture">
                                                Visualiser
                                                <i data-lucide="eye"></i>
                                            </a>
                                        </div>
                                    </div>

                                    <div class="flex flex-col gap-2 text-right">
                                        <p class="heading-1 font-normal font-title"><?php echo $activeDays * $offer->type()->price ?> € <span class="text-sm">HT</span></p>
                                        <p>pour <span class="underline"><?php echo $activeDays ?> jours</span> en ligne</p>
                                    </div>
                                </article>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>

                <?php if (empty($invoices)) { ?>
                    <p>Aucune facture. Attendez la fin du mois pour voir apparaitre vos premières
                        factures.</p>
                <?php } ?>
            </div>
        </div>
    </div>
</div>