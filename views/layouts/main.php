<?php

/** @var $this \app\core\View */

use app\core\Application;

?>

<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?php echo $this->title ?> | PACT</title>

    <link rel="icon" href="/assets/images/favicon.ico" type="image/x-icon">

    <!-- Styles -->
    <link rel="stylesheet" href="/css/dist/output.css">

    <?php if ($this->leaflet) { ?>
    <link rel="stylesheet" href="/css/parts/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.3.0/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.3.0/dist/MarkerCluster.Default.css" />

    <?php } ?>

    <?php if ($this->cssFile): ?>
    <link rel="stylesheet" href="/css/pages/<?php echo $this->cssFile ?>.css">
    <?php endif; ?>
</head>

<body
    class="<?php echo Application::$app->isAuthenticated() ? Application::$app->user->isProfessional() ? 'professional-mode' : '' : '' ?>">

    <input type="hidden" class="app-environment" value="<?php echo $_ENV['APP_ENVIRONMENT'] ?>">

    <?php if (!$this->noMain) { ?>
    <div class="height-top"></div>
    <?php } ?>

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
            <a href="/" title="retour à l'accueil">
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
                <p>
                    <?php echo Application::$app->userType ?>
                </p>
                <p>connecté</p>
            </div>
            <?php } ?>
        </div>
        <div class="navbar-right">
            <a href="/recherche" class="button sm gray icon-left icon-right no-border hidden md:flex"
                title="rechercher">
                Recherche
                <i data-lucide="search"></i>
            </a>

            <?php if (Application::$app->isAuthenticated()) { ?>
            <button class="chat-trigger button gray icon-left icon-right no-border hidden md:flex"
                title="accèder à mes messages">
                <span>Messages</span>
                <i data-lucide="message-circle"></i>
            </button>

            <!-- Notifications -->
            <div class="notification">
                <div class="notification-icon button gray icon-left icon-right no-border hidden md:flex">
                    <p>Notifications</p>
                    <svg id="icon-alert" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-bell-dot">
                        <path d="M10.268 21a2 2 0 0 0 3.464 0" />
                        <path
                            d="M13.916 2.314A6 6 0 0 0 6 8c0 4.499-1.411 5.956-2.74 7.327A1 1 0 0 0 4 17h16a1 1 0 0 0 .74-1.673 9 9 0 0 1-.585-.665" />
                        <circle cx="18" cy="8" r="3" fill="red" stroke="red" />
                    </svg>
                    <svg id="icon-default" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-bell">
                        <path d="M10.268 21a2 2 0 0 0 3.464 0" />
                        <path
                            d="M3.262 15.326A1 1 0 0 0 4 17h16a1 1 0 0 0 .74-1.673C19.41 13.956 18 12.499 18 8A6 6 0 0 0 6 8c0 4.499-1.411 5.956-2.738 7.326" />
                    </svg>
                </div>
                <div class="notification-container"></div>
            </div>

            <!-- Avatar -->
            <div class="avatar">
                <div class="image-container">
                    <img src="<?php echo Application::$app->user->avatar_url ?>"
                        alt=" <?php echo Application::$app->user->mail ?>">
                </div>

                <div class="avatar-options">

                    <?php if (Application::$app->user?->isMember()) { ?>
                    <a href="/comptes/<?php echo Application::$app->user->account_id ?>"
                        title="accèder a mon compte">Mon profil</a>
                    <?php } ?>

                    <?php if (Application::$app->user?->isProfessional()) { ?>
                    <a href="/comptes/modification" title="accàder a mon compte">Mon compte</a>
                    <a href="/dashboard" title="accèder a mon dashboard">Mon dashboard</a>
                    <?php } ?>

                    <a href="/deconnexion" title="se déconnecter">
                        Déconnexion
                        <i data-lucide="log-out" width="18"></i>
                    </a>
                </div>
            </div>
            <?php } else { ?>
            <div class="navbarLoginButtons">
                <a href="/connexion" class="link" title="se connecté">Connexion</a>
                <a href="/inscription" class="button sm" title="s'inscrire">Inscription</a>
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
            <li><a href="/" title="retour à l'accueil">Accueil</a></li>
            <li><a href="/recherche" title="accèder à la page de recherche">Rechercher</a></li>
            <li><a href="/connexion" title="se connecter">Connexion</a></li>
            <li><a href="/inscription" title="s'inscrire">Inscription</a></li>
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

    <!-- With main -->
    <?php if (!$this->noMain) { ?>
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
    <?php } ?>

    <!-- Without main -->
    <?php if ($this->noMain) { ?>
    {{content}}
    <?php } ?>


    <!-- Bottom navbar for mobile -->
    <nav class="bottom-navbar" title="retour à l'accueil">
        <a href="/" class="<?php echo Application::$app->request->getPath() === '/' ? "active" : "" ?> "
            title="accès à la page d'accueil">
            <i data-lucide="home"></i>
            <span>Accueil</span>
        </a>

        <a href="/recherche" title="accès à la page de recherche"
            class="<?php echo Application::$app->request->getPath() === "/recherche" ? "active" : "" ?>">
            <i data-lucide="search"></i>
            <span>Recherche</span>
        </a>

        <a class="chat-trigger" title="ouvrir mes messages">
            <i data-lucide="message-circle"></i>
            <span>Messages</span>
        </a>

        <!--    <a href="/avis" class="--><?php //echo Application::$app->request->getPath() === "/avis" ? "active" : "" ?>
        <!--">-->
        <!--        <i data-lucide="message-circle"></i>-->
        <!--        <span>Mes avis</span>-->
        <!--    </a>-->

        <a href="<?php echo Application::$app->isAuthenticated() ? '/comptes/' . Application::$app->user->account_id : '/connexion' ?>"
            title="accès à la page de compte"
            class="<?php echo Application::$app->request->getPath() === (Application::$app->isAuthenticated() ? '/comptes/' . Application::$app->user->account_id : '/connexion') ? "active" : "" ?>">
            <i data-lucide="user"></i>
            <span>Compte</span>
        </a>
    </nav>

    <footer class="pb-24 md:pb-4">
        <!--        <svg class="footer-background" width="1069" height="751" viewBox="0 0 1069 751" fill="none" xmlns="http://www.w3.org/2000/svg">-->
        <!--            <path d="M788.2 742.861C791.298 750.143 800.442 752.533 806.707 747.699L818.623 738.506C821.973 735.922 825.953 734.28 830.151 733.752L889.211 726.322C894.524 725.654 899.324 722.819 902.475 718.489V718.489C908.842 709.74 921.136 707.891 929.795 714.381L944.412 725.338C957.137 734.876 974.5 735.328 987.702 726.464L1012.03 710.13C1023.61 702.358 1026.37 686.507 1018.11 675.277V675.277C1011.77 666.662 1011.75 654.931 1018.07 646.299L1051.82 600.162C1052.13 599.736 1052.7 599.598 1053.17 599.833V599.833C1053.81 600.149 1054.57 599.784 1054.71 599.093L1068.54 534.608C1070.89 523.646 1062.96 513.154 1051.77 512.428V512.428C1041.58 511.767 1033.88 502.917 1034.64 492.732L1035 487.923C1035.52 480.998 1032.17 474.354 1026.3 470.647V470.647C1016.12 464.219 1014.53 450.002 1023.05 441.489L1031.65 432.895C1032.72 431.821 1033.88 430.835 1035.11 429.946L1040.05 426.384C1047.37 421.1 1051.39 412.373 1050.65 403.376L1049.85 393.599C1049.49 389.238 1046.57 385.512 1042.42 384.129L1039.16 383.043C1029.97 379.982 1030.24 366.894 1039.55 364.218V364.218C1043.77 363.004 1046.68 359.14 1046.68 354.746V334.715C1046.68 326.759 1044.08 319.022 1039.27 312.683L1033.98 305.705C1026.45 295.779 1022.06 283.828 1021.37 271.387L1017.81 206.904C1017.46 200.678 1019.79 194.6 1024.2 190.192V190.192C1029.8 184.594 1031.94 176.411 1029.81 168.785L1026.48 156.875C1022.37 142.183 1010.79 130.762 996.045 126.852L980.989 122.86C973.82 120.959 968.364 115.134 966.937 107.856V107.856C965.265 99.3351 958.13 92.9658 949.474 92.2682L941.862 91.6546C935.558 91.1465 929.272 92.835 924.071 96.4335L920.444 98.9433C911.661 105.021 899.789 103.95 892.234 96.3999V96.3999C890.282 94.4491 887.982 92.8809 885.453 91.7764L859.246 80.3312C839.422 71.6732 816.73 72.5341 797.618 82.6693L786.634 88.4942C780.563 91.7141 775.664 96.7702 772.638 102.941V102.941C767.532 113.351 757.28 120.266 745.715 121.102L739.727 121.534C728.113 122.374 716.654 118.473 707.964 110.721L690.328 94.9906C679.825 85.6222 665.403 81.9839 651.713 85.2491V85.2491C637.084 88.7381 621.696 84.3343 611.127 73.6347L594.441 56.7416C590.584 52.8363 585.323 50.6383 579.834 50.6383V50.6383C573.522 50.6383 567.561 47.7348 563.669 42.7651L560.545 38.7754C554.682 31.2873 545.7 26.9125 536.189 26.9125H518.796C515.531 26.9125 512.472 28.5118 510.609 31.1937V31.1937C506.213 37.5199 496.609 36.6838 493.373 29.6932L487.66 17.3537C485.749 13.225 482.326 9.98717 478.098 8.30765L467.001 3.89973C452.82 -1.7331 436.8 -0.24113 423.903 7.91351L406.695 18.7941C398.702 23.8484 393.856 32.6461 393.856 42.1035V42.1035C393.856 53.7247 386.571 64.0988 375.64 68.0439L361.791 73.0419C358.422 74.2579 354.867 74.8798 351.285 74.8798V74.8798C339.984 74.8798 329.582 68.7206 324.148 58.8121L317.562 46.8009C308.812 30.8449 292.624 20.3588 274.485 18.8968L271.367 18.6455C251.481 17.0427 232.102 25.4478 219.684 41.0621L213.381 48.9868C209.511 53.8531 206.23 59.1599 203.605 64.7966L184.428 105.991C177.106 121.719 163.755 133.831 147.392 139.592L136.074 143.577C130.545 145.524 125.776 149.177 122.457 154.009L117.111 161.791C110.175 171.888 95.8103 173.289 87.0534 164.723V164.723C82.3739 160.146 75.7287 158.188 69.3147 159.496L58.28 161.747C40.8332 165.305 27.6846 179.729 25.7513 197.43L14.1274 303.86C13.3757 310.743 12.085 317.556 10.2673 324.237L1.6573 355.882C-1.34196 366.905 -0.174383 378.647 4.93691 388.864L7.73343 394.454C11.1203 401.224 16.9156 406.483 23.9814 409.199V409.199C33.8305 412.985 41.0053 421.616 42.9277 431.992L53.3776 488.389C55.217 498.316 67.668 501.787 74.3777 494.243V494.243C76.9882 491.308 80.8578 489.818 84.7627 490.244L120.284 494.122C131.342 495.33 141.435 487.738 143.342 476.779V476.779C144.991 467.304 152.844 460.145 162.432 459.379L162.94 459.338C175.001 458.374 185.529 467.442 186.361 479.512L186.733 484.901C187.462 495.468 194.654 504.481 204.796 507.538L214.038 510.324C217.949 511.503 222.188 510.293 224.887 507.227V507.227C231.248 500.004 243.164 504.502 243.164 514.127V523.253C243.164 534.681 256.197 541.217 265.355 534.382L275.028 527.164C279.397 523.904 284.91 522.574 290.285 523.486L309.649 526.769C327.502 529.796 345.248 520.852 353.436 504.7V504.7C357.241 497.196 363.244 491.031 370.645 487.028L467.817 434.476C474.393 430.919 478.491 424.044 478.491 416.568V409.111C478.491 401.985 484.267 396.209 491.392 396.209V396.209C498.518 396.209 504.294 401.985 504.294 409.111V431.895C504.294 445.693 512.248 458.254 524.721 464.154L542.593 472.608C560.143 480.909 570.525 499.401 568.473 518.708V518.708C566.759 534.838 573.733 550.667 586.792 560.29L609.658 577.14C620.312 584.991 626.602 597.438 626.602 610.673V621.776C626.602 634.917 635.772 646.276 648.617 649.048V649.048C662.078 651.953 671.407 664.246 670.581 677.992L669.961 688.328C669.381 697.986 676.592 706.352 686.23 707.202L687.002 707.27C696.4 708.099 703.35 716.383 702.532 725.781V725.781C701.663 735.77 709.537 744.359 719.563 744.359H758.317C762.541 744.359 766.269 741.602 767.507 737.564V737.564C770.136 728.991 782.032 728.367 785.543 736.618L788.2 742.861Z" fill="url(#paint0_radial_3490_10332)"/>-->
        <!--            <defs>-->
        <!--                <radialGradient id="paint0_radial_3490_10332" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(545.371 648.122) rotate(-90.2296) scale(637.854 900.32)">-->
        <!--                    <stop offset="0.685" stop-color="#FFD884" stop-opacity="0"/>-->
        <!--                    <stop offset="1" stop-color="#FFD884" stop-opacity="0.5"/>-->
        <!--                </radialGradient>-->
        <!--            </defs>-->
        <!--        </svg>-->


        <div class="footer-parts">
            <div>
                <p>À propos</p>
                <a href="" class="link simple" title="à propos de PACT">À propos de PACT</a>
                <a href="" class="link simple" title="Règlement">Règlements</a>
            </div>
            <div>
                <p>Explorez</p>
                <a href="" class="link simple" title="écrire un avis">Écrire un avis</a>
                <a href="/inscription" class="link simple" title="s'inscrire">S'inscrire</a>
            </div>
            <div>
                <p>Utilisez nos solutions</p>
                <a href="/inscription" class="link simple" title="s'inscrire">Professionnel</a>
            </div>
        </div>
        <nav>
            <div class="footer-conditions">
                <img id="logo" src="/assets/images/logoSmallVisitor.svg" alt="Logo PACT">
                <div class="flex flex-col gap-1">
                    <p>@ 2024 PACT Tous droits réservés.</p>
                    <div id="links">
                        <a class="link small" href="/conditions" title="Conditions d'utilisation">Conditions
                            d'utilisation</a>
                        <a class="link small" href="/mentions" title="Mentions légales">Mentions légales</a>
                        <a class="link small" href="" title="Plan du site">Plan du site</a>
                        <a class="link small" href="/conditions" title="Contactez-nous">Contactez-nous</a>
                    </div>
                </div>
            </div>
            <div class="footer-networks">
                <a href="https://www.instagram.com/" target="_blank" class="button gray only-icon"
                    title="notre instagram">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-instagram">
                        <rect width="20" height="20" x="2" y="2" rx="5" ry="5" />
                        <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" />
                        <line x1="17.5" x2="17.51" y1="6.5" y2="6.5" />
                    </svg>
                </a>
                <a href="https://www.facebook.com/" target="_blank" class="button gray only-icon"
                    title="notre facebook">
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/4.8.1/socket.io.js"
        integrity="sha512-8BHxHDLsOHx+flIrQ0DrZcea7MkHqRU5GbTHmbdzMRnAaoCIkZ97PqZcXJkKZckMMhqfoeaJE+DNUVuyoQsO3Q=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <?php if ($this->leaflet) { ?>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.3.0/dist/leaflet.markercluster.js"></script>
    <?php } ?>

    <?php if ($this->threejs) { ?>
    <script type="importmap">
        {
                                                                                                                                          "imports": {
                                                                                                                                            "three": "https://cdn.jsdelivr.net/npm/three@0.172.0/build/three.module.js"
                                                                                                                                          }
                                                                                                                                        }
                                                                                                                
                        </script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.7/dist/gsap.min.js"></script>
    <?php } ?>

    <script type="module" src="/js/main.js"></script>

    <?php if ($this->jsFile) { ?>
    <script type="module" src="/js/pages/<?php echo $this->jsFile ?>.js"></script>
    <?php } ?>
</body>

</html>