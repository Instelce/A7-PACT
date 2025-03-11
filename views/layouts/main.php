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
            <a href="/recherche" class="button sm gray icon-left icon-right no-border hidden md:flex">
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
        <!--<svg class="footer-background" width="1076" height="764" viewBox="0 0 1076 764" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M791.2 748.861C794.298 756.143 803.442 758.533 809.707 753.699L821.623 744.506C824.973 741.922 828.953 740.28 833.151 739.752L892.211 732.322C897.524 731.654 902.324 728.819 905.475 724.489V724.489C911.842 715.74 924.136 713.891 932.795 720.381L947.412 731.338C960.137 740.876 977.5 741.328 990.702 732.464L1015.03 716.13C1026.61 708.358 1029.37 692.507 1021.11 681.277V681.277C1014.77 672.662 1014.75 660.931 1021.07 652.299L1054.82 606.162C1055.13 605.736 1055.7 605.598 1056.17 605.833V605.833C1056.81 606.149 1057.57 605.784 1057.71 605.093L1071.54 540.608C1073.89 529.646 1065.96 519.154 1054.77 518.428V518.428C1044.58 517.767 1036.88 508.917 1037.64 498.732L1038 493.923C1038.52 486.998 1035.17 480.354 1029.3 476.647V476.647C1019.12 470.219 1017.53 456.002 1026.05 447.489L1034.65 438.895C1035.72 437.821 1036.88 436.835 1038.11 435.946L1043.05 432.384C1050.37 427.1 1054.39 418.373 1053.65 409.376L1052.85 399.599C1052.49 395.238 1049.57 391.512 1045.42 390.129L1042.16 389.043C1032.97 385.982 1033.24 372.894 1042.55 370.218V370.218C1046.77 369.004 1049.68 365.14 1049.68 360.746V340.715C1049.68 332.759 1047.08 325.022 1042.27 318.683L1036.98 311.705C1029.45 301.779 1025.06 289.828 1024.37 277.387L1020.81 212.904C1020.46 206.678 1022.79 200.6 1027.2 196.192V196.192C1032.8 190.594 1034.94 182.411 1032.81 174.785L1029.48 162.875C1025.37 148.183 1013.79 136.762 999.045 132.852L983.989 128.86C976.82 126.959 971.364 121.134 969.937 113.856V113.856C968.265 105.335 961.13 98.9658 952.474 98.2682L944.862 97.6546C938.558 97.1465 932.272 98.835 927.071 102.434L923.444 104.943C914.661 111.021 902.789 109.95 895.234 102.4V102.4C893.282 100.449 890.982 98.8809 888.453 97.7764L862.246 86.3312C842.422 77.6732 819.73 78.5341 800.618 88.6693L789.634 94.4942C783.563 97.7141 778.664 102.77 775.638 108.941V108.941C770.532 119.351 760.28 126.266 748.715 127.102L742.727 127.534C731.113 128.374 719.654 124.473 710.964 116.721L693.328 100.991C682.825 91.6222 668.403 87.9839 654.713 91.2491V91.2491C640.084 94.7381 624.696 90.3343 614.127 79.6347L597.441 62.7416C593.584 58.8363 588.323 56.6383 582.834 56.6383V56.6383C576.522 56.6383 570.561 53.7348 566.669 48.7651L563.545 44.7754C557.682 37.2873 548.7 32.9125 539.189 32.9125H521.796C518.531 32.9125 515.472 34.5118 513.609 37.1937V37.1937C509.213 43.5199 499.609 42.6838 496.373 35.6932L490.66 23.3537C488.749 19.225 485.326 15.9872 481.098 14.3077L470.001 9.89973C455.82 4.2669 439.8 5.75887 426.903 13.9135L409.695 24.7941C401.702 29.8484 396.856 38.6461 396.856 48.1035V48.1035C396.856 59.7247 389.571 70.0988 378.64 74.0439L364.791 79.0419C361.422 80.2579 357.867 80.8798 354.285 80.8798V80.8798C342.984 80.8798 332.582 74.7206 327.148 64.8121L320.562 52.8009C311.812 36.8449 295.624 26.3588 277.485 24.8968L274.367 24.6455C254.481 23.0427 235.102 31.4478 222.684 47.0621L216.381 54.9868C212.511 59.8531 209.23 65.1599 206.605 70.7966L187.428 111.991C180.106 127.719 166.755 139.831 150.392 145.592L139.074 149.577C133.545 151.524 128.776 155.177 125.457 160.009L120.111 167.791C113.175 177.888 98.8103 179.289 90.0534 170.723V170.723C85.3739 166.146 78.7287 164.188 72.3147 165.496L61.28 167.747C43.8332 171.305 30.6846 185.729 28.7513 203.43L17.1274 309.86C16.3757 316.743 15.085 323.556 13.2673 330.237L4.6573 361.882C1.65804 372.905 2.82562 384.647 7.93691 394.864L10.7334 400.454C14.1203 407.224 19.9156 412.483 26.9814 415.199V415.199C36.8305 418.985 44.0053 427.616 45.9277 437.992L56.3776 494.389C58.217 504.316 70.668 507.787 77.3777 500.243V500.243C79.9882 497.308 83.8578 495.818 87.7627 496.244L123.284 500.122C134.342 501.33 144.435 493.738 146.342 482.779V482.779C147.991 473.304 155.844 466.145 165.432 465.379L165.94 465.338C178.001 464.374 188.529 473.442 189.361 485.512L189.733 490.901C190.462 501.468 197.654 510.481 207.796 513.538L217.038 516.324C220.949 517.503 225.188 516.293 227.887 513.227V513.227C234.248 506.004 246.164 510.502 246.164 520.127V529.253C246.164 540.681 259.197 547.217 268.355 540.382L278.028 533.164C282.397 529.904 287.91 528.574 293.285 529.486L312.649 532.769C330.502 535.796 348.248 526.852 356.436 510.7V510.7C360.241 503.196 366.244 497.031 373.645 493.028L470.817 440.476C477.393 436.919 481.491 430.044 481.491 422.568V415.111C481.491 407.985 487.267 402.209 494.392 402.209V402.209C501.518 402.209 507.294 407.985 507.294 415.111V437.895C507.294 451.693 515.248 464.254 527.721 470.154L545.593 478.608C563.143 486.909 573.525 505.401 571.473 524.708V524.708C569.759 540.838 576.733 556.667 589.792 566.29L612.658 583.14C623.312 590.991 629.602 603.438 629.602 616.673V627.776C629.602 640.917 638.772 652.276 651.617 655.048V655.048C665.078 657.953 674.407 670.246 673.581 683.992L672.961 694.328C672.381 703.986 679.592 712.352 689.23 713.202L690.002 713.27C699.4 714.099 706.35 722.383 705.532 731.781V731.781C704.663 741.77 712.537 750.359 722.563 750.359H761.317C765.541 750.359 769.269 747.602 770.507 743.564V743.564C773.136 734.991 785.032 734.367 788.543 742.618L791.2 748.861Z" fill="url(#paint0_radial_3490_10331)"/>
            <defs>
                <radialGradient id="paint0_radial_3490_10331" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(548.371 654.122) rotate(-90.2296) scale(637.854 900.32)">
                    <stop offset="0.685" stop-color="#FFD884" stop-opacity="0"/>
                    <stop offset="1" stop-color="#FFD884" stop-opacity="0.5"/>
                </radialGradient>
            </defs>
        </svg>-->

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