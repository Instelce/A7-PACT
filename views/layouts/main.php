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

    <?php if ($this->leaflet) { ?>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <?php } ?>

    <?php if ($this->cssFile): ?>
        <link rel="stylesheet" href="/css/pages/<?php echo $this->cssFile ?>.css">
    <?php endif; ?>
</head>

<body
    class="<?php echo Application::$app->isAuthenticated() ? Application::$app->user->isProfessional() ? 'professional-mode' : '' : '' ?>">

    <input class="app-environment" value="<?php echo $_ENV['APP_ENVIRONMENT'] ?>">

    <div class="height-top"></div>

    <!-- Loader -->
    <div class="loader">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
            class="lucide lucide-loader-circle">
            <path d="M21 12a9 9 0 1 1-6.219-8.56" />
        </svg>
    </div>


    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-left">
            <a href="/">
                <img id="logo" src="/assets/images/logoVisitor.svg" alt="" width="100">
            </a>

            <!-- For dev -->
            <?php if (Application::$app->isAuthenticated() && $_ENV['APP_ENVIRONMENT'] === 'dev') { ?>
                <div class="flex gap-1">
                    <?php if (Application::$app->user->isPrivateProfessional()) { ?>
                        <p>private</p>
                    <?php } else { ?>
                        <p>public</p>
                    <?php } ?>
                    <p><?php echo Application::$app->userType ?></p>
                    <p>connecté</p>
                </div>
            <?php } ?>
        </div>
        <div class="navbar-right">
            <a href="/recherche" class="button gray icon-left icon-right no-border hidden md:flex">
                Recherche
                <i data-lucide="search"></i>
            </a>

        <?php if (Application::$app->isAuthenticated()) { ?>
            <button class="chat-trigger button gray icon-left icon-right no-border hidden md:flex">
                <span>Messages</span>
                <i data-lucide="message-circle"></i>
            </button>

            <!-- Notifications -->
            <div class="notification">
                <div class="notification-icon button gray icon-left icon-right no-border hidden md:flex">
                    <p>Notifications</p>
                    <svg id="icon-alert" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-bell-dot"><path d="M10.268 21a2 2 0 0 0 3.464 0"/><path d="M13.916 2.314A6 6 0 0 0 6 8c0 4.499-1.411 5.956-2.74 7.327A1 1 0 0 0 4 17h16a1 1 0 0 0 .74-1.673 9 9 0 0 1-.585-.665"/><circle cx="18" cy="8" r="3" fill="red" stroke="red"/></svg>
                    <svg id="icon-default" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-bell"><path d="M10.268 21a2 2 0 0 0 3.464 0"/><path d="M3.262 15.326A1 1 0 0 0 4 17h16a1 1 0 0 0 .74-1.673C19.41 13.956 18 12.499 18 8A6 6 0 0 0 6 8c0 4.499-1.411 5.956-2.738 7.326"/></svg>
                </div>
                <div class="notification-container"></div>
            </div>

            <!-- Avatar -->
            <div class="avatar">
                <div class="image-container">
                    <img src="<?php echo Application::$app->user->avatar_url ?>"
                         alt="<?php echo Application::$app->user->mail ?>">
                </div>

                    <div class="avatar-options">

                        <?php if (Application::$app->user?->isMember()) { ?>
                            <a href="/comptes/<?php echo Application::$app->user->account_id ?>">Mon profil</a>
                        <?php } ?>

                        <?php if (Application::$app->user?->isProfessional()) { ?>
                            <a href="/comptes/modification">Mon compte</a>
                            <a href="/dashboard">Mon dashboard</a>
                        <?php } ?>

                        <a href="/deconnexion">
                            Déconnexion
                            <i data-lucide="log-out" width="18"></i>
                        </a>
                    </div>
                </div>
            <?php } else { ?>
                <div class="navbarLoginButtons">
                    <a href="/connexion" class="link">Connexion</a>
                    <a href="/inscription" class="button sm">Inscription</a>
                </div>
            <?php } ?>

            <div class="nav-burger" id="nav-burger">
                <span></span>
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </nav>

    <!-- Menu of the navbar -->
    <div id="menu" class="menu-hidden">
        <ul>
            <li><a href="/">Accueil</a></li>
            <li><a href="/recherche">Rechercher</a></li>
            <li><a href="/connexion">Connexion</a></li>
            <li><a href="/inscription">Inscription</a></li>
        </ul>
    </div>

    <?php if ($this->waves) { ?>
        <div class="wave">
            <svg class="waveSvg" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                 viewBox="0 24 150 28" preserveAspectRatio="none">
                <defs>
                    <path id="gentle-wave" d="M-160 44c30 0
            58-18 88-18s
            58 18 88 18
            58-18 88-18
            58 18 88 18
            v44h-352z" />
                </defs>
                <g class="waves">
                    <use xlink:href="#gentle-wave" x="50" y="0" fill="#FFA800" fill-opacity="1" />
                    <use xlink:href="#gentle-wave" x="50" y="3" fill="#00A2FF" fill-opacity="1" />
                    <use xlink:href="#gentle-wave" x="50" y="6" fill="#0057FF" fill-opacity="1" />
                </g>
            </svg>
        </div>
    <?php } ?>


    <!-- Tchatator member client -->
    <?php if (Application::$app->userType === 'member') { ?>
        <div class="chat-container hidden top-navbar">

            <!-- All conversations -->
            <div class="chat-page conversations-page">
                <header>
                    <h2>Vos conversations</h2>
                </header>

                <!-- Generated in JS -->
                <div class="conversations-gen">
                </div>
            </div>

            <!-- All messages with a user -->
            <div class="chat-page messages-page !hidden">
                <header>
                    <div class="recipient-info">
                        <img src="" alt="" class="recipient-avatar">
                        <p class="recipient-name"></p>
                    </div>
                    <button class="goto-conversations">
                        <i data-lucide="x"></i>
                    </button>
                </header>

                <!-- Generated in JS -->
                <div class="messages-container">
                </div>

                <!-- Writing indicator -->
                <div class="writing-indicator !hidden">
                    <span style="--i:1"></span>
                    <span style="--i:2"></span>
                    <span style="--i:3"></span>
                </div>

                <div class="chat-bottom">
                    <label for="message-writer" class="hidden">Message</label>
                    <textarea id="message-writer" class="message-writer" cols="30" rows="3"></textarea>
                    <button class="send-button">
                        <i data-lucide="send"></i>
                    </button>
                </div>
            </div>
        </div>
    <?php } ?>


    <main class="main-container">
        <!-- Show alert -->
        <?php if (Application::$app->session->getFlash('success')): ?>
            <div class="alert success">
                <?php echo Application::$app->session->getFlash('success') ?>
            </div>
        <?php endif; ?>

        <!-- Content of the view-->
        {{content}}
    </main>


    <!-- Bottom navbar for mobile -->
    <nav class="bottom-navbar">
        <a href="/" class="<?php echo Application::$app->request->getPath() === '/' ? "active" : "" ?>">
            <i data-lucide="home"></i>
            <span>Accueil</span>
        </a>

        <a href="/recherche"
            class="<?php echo Application::$app->request->getPath() === "/recherche" ? "active" : "" ?>">
            <i data-lucide="search"></i>
            <span>Recherche</span>
        </a>

        <a class="chat-trigger">
            <i data-lucide="message-circle"></i>
            <span>Messages</span>
        </a>

        <!--    <a href="/avis" class="--><?php //echo Application::$app->request->getPath() === "/avis" ? "active" : "" ?>
        <!--">-->
        <!--        <i data-lucide="message-circle"></i>-->
        <!--        <span>Mes avis</span>-->
        <!--    </a>-->

        <a href="<?php echo Application::$app->isAuthenticated() ? '/comptes/' . Application::$app->user->account_id : '/connexion' ?>"
            class="<?php echo Application::$app->request->getPath() === (Application::$app->isAuthenticated() ? '/comptes/' . Application::$app->user->account_id : '/connexion') ? "active" : "" ?>">
            <i data-lucide="user"></i>
            <span>Compte</span>
        </a>
    </nav>

    <footer class="pb-24 md:pb-4">
        <div class="footer-parts">
            <div>
                <p>À propos</p>
                <a href="" class="link simple">À propos de PACT</a>
                <a href="" class="link simple">Règlements</a>
            </div>
            <div>
                <p>Explorez</p>
                <a href="" class="link simple">Écrire un avis</a>
                <a href="/inscription" class="link simple">S'inscrire</a>
            </div>
            <div>
                <p>Utilisez nos solutions</p>
                <a href="/inscription" class="link simple">Professionnel</a>
            </div>
        </div>
        <nav>
            <div class="footer-conditions">
                <img id="logo" src="/assets/images/logoSmallVisitor.svg" alt="Logo PACT">
                <div class="flex flex-col gap-1">
                    <p>@ 2024 PACT Tous droits réservés.</p>
                    <div id="links">
                        <a class="link small" href="/conditions">Conditions d'utilisation</a>
                        <a class="link small" href="/mentions">Mentions légales</a>
                        <a class="link small" href="">Plan du site</a>
                        <a class="link small" href="/conditions">Contactez-nous</a>
                    </div>
                </div>
            </div>
            <div class="footer-networks">
                <a href="https://www.instagram.com/" target="_blank" class="button gray only-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-instagram">
                        <rect width="20" height="20" x="2" y="2" rx="5" ry="5" />
                        <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" />
                        <line x1="17.5" x2="17.51" y1="6.5" y2="6.5" />
                    </svg>
                </a>
                <a href="https://www.facebook.com/" target="_blank" class="button gray only-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-facebook">
                        <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z" />
                    </svg>
                </a>
            </div>
        </nav>
        <div class="footer-trip">
            <img id="tripenarvor" src="/assets/images/TripEnArvorLogo.svg" alt="Logo TripEnArvor">
            <p>Plateforme proposée par <b>TripEnArvor</b></p>
        </div>
        <div class="footer-finance">
            <p>Projet financé par la <a>Région Bretagne</a> et par le <a>Conseil
                    général des Côtes d'Armor</a>.</p>
        </div>
    </footer>


    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/4.8.1/socket.io.js" integrity="sha512-8BHxHDLsOHx+flIrQ0DrZcea7MkHqRU5GbTHmbdzMRnAaoCIkZ97PqZcXJkKZckMMhqfoeaJE+DNUVuyoQsO3Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <?php if ($this->leaflet) { ?>
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <?php } ?>

    <script type="module" src="/js/main.js"></script>


    <?php if ($this->jsFile) { ?>
        <script type="module" src="/js/pages/<?php echo $this->jsFile ?>.js"></script>
    <?php } ?>
</body>

</html>