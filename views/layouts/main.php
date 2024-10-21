<?php

/** @var $this \app\core\View */

use app\core\Application;

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $this->title ?> | PACT</title>

    <link rel="icon" href="/assets/images/favicon.ico" type="image/x-icon">

    <!-- Styles -->
    <link rel="stylesheet" href="/css/dist/output.css">
    <link rel="stylesheet" href="/css/main.css">

    <?php if ($this->cssFile): ?>
        <link rel="stylesheet"
              href="/css/pages/<?php echo $this->cssFile ?>.css">
    <?php endif; ?>
</head>

<body>

<div class="height-top"></div>

<!-- Loader -->
<div class="loader">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
         viewBox="0 0 24 24" fill="none" stroke="currentColor"
         stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
         class="lucide lucide-loader-circle">
        <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
    </svg>
</div>


<!-- Navbar -->
<nav class="navbar">
    <div class="nav-burger" id="nav-burger">
        <span></span>
        <span></span>
        <span></span>
        <span></span>
    </div>
    <div class="flex gap-4 items-center">
        <a href="/">
            <img id="logo" src="/assets/images/logoVisitor.svg" alt="" width="100">
        </a>

        <?php if (Application::$app->isAuthenticated()) { ?>
            <p><?php echo Application::$app->userType . " connecté"?></p>
        <?php } ?>
    </div>
    <div class="row">
        <a href="/recherche">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                 viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="1" stroke-linecap="round" stroke-linejoin="round"
                 class="lucide lucide-search">
                <circle cx="11" cy="11" r="8"/>
                <path d="m21 21-4.3-4.3"/>
            </svg>
        </a>

        <?php if (Application::$app->isAuthenticated()) { ?>
            <a href="/deconnexion" class="link">Déconnexion</a>
        <?php } else { ?>
            <a href="/connexion" class="button">Connexion</a>
        <?php } ?>

    </div>
</nav>

<!-- Menu of the navbar -->
<div id="menu" class="menu-hidden">
    <ul>
        <li><a href="/">Accueil</a></li>
        <li><a href="/">Rechercher</a></li>
        <li><a href="/login">Connexion</a></li>
    </ul>
</div>


<main class="main-container">
    <!-- Show alert -->
    <?php if (Application::$app->session->getFlash('success')): ?>
        <div class="alert alert-success">
            <?php echo Application::$app->session->getFlash('success') ?>
        </div>
    <?php endif; ?>

    <!-- Content of the view-->
    {{content}}
</main>


<footer>
    <div class="footer-parts">
        <div>
            <p>À propos</p>
            <a href="">À propos de PACT</a>
            <a href="">Règlements</a>
        </div>
        <div>
            <p>Explorez</p>
            <a href="">Écrire un avis</a>
            <a href="">S'inscrire</a>
        </div>
        <div>
            <p>Utilisez nos solutions</p>
            <a href="">Professionnel</a>
        </div>
    </div>
    <nav>
        <div class="footer-conditions">
            <img id="logo" src="/assets/images/logoSmallVisitor.svg"
                 alt="Logo PACT">
            <div class="flex flex-col gap-1">
                <p>@ 2024 PACT Tous droits réservés.</p>
                <div id="links">
                    <a class="link small" href="">Conditions d'utilisation</a>
                    <a class="link small" href="">Confidentialité et
                        utilisation des cookies</a>
                    <a class="link small" href="">Plan du site</a>
                    <a class="link small" href="">Contactez-nous</a>
                </div>
            </div>
        </div>
        <div class="footer-networks">
            <a href="https://www.instagram.com/"
               class="button gray only-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24"
                     height="24" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="1"
                     stroke-linecap="round" stroke-linejoin="round"
                     class="lucide lucide-instagram">
                    <rect width="20" height="20" x="2" y="2" rx="5" ry="5"/>
                    <path
                        d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
                    <line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/>
                </svg>
            </a>
            <a href="https://www.facebook.com/"
               class="button gray only-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24"
                     height="24" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="1"
                     stroke-linecap="round" stroke-linejoin="round"
                     class="lucide lucide-facebook">
                    <path
                        d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
                </svg>
            </a>
        </div>
    </nav>
    <div class="footer-trip">
        <img id="tripenarvor" src="/assets/images/TripEnArvorLogo.svg"
             alt="Logo TripEnArvor">
        <p>Plateforme proposée par <b>TripEnArvor</b></p>
    </div>
    <div class="footer-finance">
        <p>Projet financé par la <a>Région Bretagne</a> et par le <a>Conseil
                général des Côtes d'Armor</a>.</p>
    </div>
</footer>


<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
<script type="module" src="/js/main.js"></script>

<?php if ($this->jsFile) ?>
    <script type="module" src="/js/pages/<?php echo $this->jsFile ?>.js"></script>
</body>

</html>