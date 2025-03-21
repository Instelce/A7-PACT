<?php
/** @var $this \app\core\View */

use app\core\Application;

$this->title = "Avis reçus";
$this->cssFile = "dashboard/avis";
$this->jsFile = "dashboard/avis";

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

        <div class="tab-button pro" selected>
            <i data-lucide="message-circle"></i>
            Avis reçus
        </div>

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


    <!-- Page content -->
    <div class="page-content">
        <!-- Header with search bar and filter buttons -->
        <header>
            <x-input rounded>
                <i slot="icon-left" data-lucide="search" class="w-[18px] h-[18px]"></i>
                <input slot="input" id="search-input" type="text" placeholder="Rechercher un avis">
            </x-input>

            <div class="filters grid grid-cols-3 gap-2">
                <label for="a" class="button gray">
                    <i data-lucide="message-square-dot"></i>
                    Non lus
                    <input id="filter-non-lu" type="radio" name="filter" value="read">
                </label>
                <label for="a" class="button gray">
                    <i data-lucide="ban"></i>
                    Blacklistés
                    <input id="filter-blackliste" type="radio" name="filter" value="blacklisted">
                </label>

<!--                <label for="a" class="button gray">-->
<!--                    <i data-lucide="message-square-more"></i>-->
<!--                    Non répondu-->
<!--                    <input id="filter-non-repondu" type="radio" name="filter" value="replied">-->
<!--                </label>-->

            </div>
        </header>

        <!-- Opinions, generated in js file -->
        <div class="opinions-container">
            <div id="loader-section" class="loader-section">
            </div>
        </div>
    </div>

</div>