<?php
use app\core\Application;
?>

<div class="flex gap-4">

    <!-- Tabs button -->
    <div class="flex flex-col w-[250px] h-fit sticky top-navbar-height">
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

</div>