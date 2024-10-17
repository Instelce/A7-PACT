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
            <img id="logo" src="../../assets/images/logoBlue.png" alt="">
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
    <div id="footer_bot">
        <x-footer></x-footer>
    </div>

    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

    <script type="module" src="/js/main.js"></script>
    <script src="/js/stores/navbarBurger.js"></script>

</body>

</html>