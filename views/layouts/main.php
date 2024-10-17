<?php

use app\core\Application;

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $this->title ?></title>
    <link rel="stylesheet" href="/css/main.css">
    <?php if ($this->cssFile): ?>
        <link rel="stylesheet" href="/css/pages/<?php echo $this->cssFile ?>.css">
    <?php endif; ?>
</head>

<body>

    <nav class="navbar">
        <div id="nav-icon3">
            <span></span>
            <span></span>
            <span></span>
            <span></span>
        </div>
        <div>
        <a href="/">
            <img id="logo" src="/assets/images/logoVisitor.svg" alt="">
        </a>
            </div>
        <div class="row">
            <a href="/">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            </a>
            <x-button href="/login">Connexion</x-button>
        </div>
    </nav>

    <div id="menu" class="menu-hidden">
        <ul>
            <li><a href="/">Accueil</a></li>
            <li><a href="/">Rechercher</a></li>
            <li><a href="/login">Connexion</a></li>
        </ul>
    </div>

    <div>
        <!-- Show alert -->
        <?php if (Application::$app->session->getFlash('success')): ?>
            <div class="alert alert-success">
                <?php echo Application::$app->session->getFlash('success') ?>
            </div>
        <?php endif; ?>
        {{content}}
    </div>
    <footer>
        <div id="parts">
            <div id="about">
                <p>À propos</p>
                <a href="">À propos de PACT</a>
                <a href="">Règlements</a>
            </div>
            <div id="explore">
                <p>Explorez</p>
                <a href="">Écrire un avis</a>
                <a href="">S'inscrire</a>
            </div>
            <div id="solutions">
                <p>Utilisez nos solutions</p>
                <a href="">Professionnel</a>
            </div>
        </div>
        <nav>
            <div id="conditions">
                <img id="logo" src="/assets/images/logoSmallVisitor.svg" alt="Logo PACT">
                <div>
                    <p>@ 2024 PACT Tous droits réservés.</p>
                    <div id="links">
                        <a class="blueLink" href="">Conditions d'utilisation</a>
                        <a class="blueLink" href="">Confidentialité et utilisation des cookies</a>
                        <a class="blueLink" href="">Plan du site</a>
                        <a class="blueLink" href="">Contactez-nous</a>
                    </div>
                </div>
            </div>
            <div id="networks">
                <a href="https://www.instagram.com/"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-instagram"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/></svg></a>
                <a href="https://www.facebook.com/"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-facebook"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg></a>
            </div>
        </nav>
        <div id="trip">
            <img id="tripenarvor" src="/assets/images/TripEnArvorLogo.svg" alt="Logo TripEnArvor">
            <p>Plateforme proposée par <b>TripEnArvor</b></p>
        </div>
        <div id="finance">
            <p>Projet financé par la</p>
            <a href="">Région Bretagne</a>
            <p>et par le</p>
            <a href="">Conseil Général des Côtes d’Armor.</a>
        </div>
    </footer>

    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

    <script type="module" src="/js/main.js"></script>
    <script>
        let navIcon = document.getElementById('nav-icon3');
        let menu = document.getElementById('menu');
        let closeMenu = document.getElementById('close-menu');

        navIcon.addEventListener('click', function () {
            navIcon.classList.toggle('open');
            menu.classList.toggle('menu-hidden');
            menu.classList.toggle('menu-visible');
        });

        closeMenu.addEventListener('click', function () {
            menu.classList.remove('menu-visible');
            menu.classList.add('menu-hidden');
            navIcon.classList.remove('open');
        });
    </script>

</body>

</html>