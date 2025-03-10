<?php
/** @var $this \app\core\View */

/** @var $offersALaUne array */

use app\core\Application;

$this->title = "Home";
$this->cssFile = "home";
$this->jsFile = "home";
$this->threejs = true;
$this->noMain = true;
//$this->waves = true;

// $this->waves = true;

?>

<header>
    <div id="background"></div>

    <div class="header-content">
        <h1 class="title">
            Découvrez
            <div class="title-slide-container">
                <span class="title-slides">
                    <span style="--i: 0">Lannion</span>
                    <span style="--i: 1">Saint Brieuc</span>
                    <span style="--i: 2">Plouha</span>
                    <span style="--i: 3">Bréhat</span>
                    <span style="--i: 4">Trébeurden</span>
                </span>
            </div>
        </h1>

        <x-input rounded class="lg:w-[600px] sm:w-full">
            <input slot="input" type="text"
                   placeholder="Recherchez des activités, visites, spectacles..."
                   id="searchBar">
            <button slot="button" class="button only-icon sm" id="searchButton">
                <i data-lucide="search" stroke-width="2"></i>
            </button>
        </x-input>

    </div>
</header>

<section>
    <div class="home-display">
        <h1 class="home-category-title">Destinations phares</h1>

        <!-- Carousel -->
        <div class="carousel-gen" data-slides-visible="3" data-slides-to-scroll="1">

            <?php foreach ($offersALaUne as $offer) { ?>
                <a href="/offres/<?php echo $offer["id"]; ?>">

                    <div class="home-card">
                        <!-- Image -->
                        <div class="image-container">
                            <img src="<?php echo $offer["image"]; ?>"
                                 alt="Image de <?php echo $offer['title']; ?>">
                            <!-- Nice background on hover -->
                            <img class="image-bg" src="<?php echo $offer["image"]; ?>"
                                 alt="Image de <?php echo $offer['title']; ?>">

                            <!-- Localization -->
                            <div class="flex gap-2 justify-center items-center localization">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15"
                                     viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="3"
                                     stroke-linecap="round"
                                     stroke-linejoin="round" class="lucide lucide-map-pin">
                                    <path
                                        d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/>
                                    <circle cx="12" cy="10" r="3"/>
                                </svg>
                                <p><?php echo $offer["location"]; ?></p>
                            </div>
                        </div>

                        <div class="card-content">
                            <!-- Title and summary -->
                            <h2><?php echo $offer["title"]; ?></h2>
                            <p class="summary text-ellipsis"><?php echo $offer["summary"]; ?></p>

                            <div class="flex flex-col gap-2">
                                <!-- Type + Professional -->
                                <p><?php echo $offer["type"]; ?> proposé par <a
                                        href="/"><?php echo $offer["author"]; ?></a></p>

                                <!-- Stars + Avis -->
                                <div class="flex gap-2">
                                    <div class="stars" data-number="<?php echo $offer["rating"] ?>">
                                    </div>
                                    <div class="flex gap-1">
                                        <p><?php echo $offer["ratingsCount"] ?></p>
                                        <p>Avis</p>
                                    </div>
                                </div>

                                <div class="price">
                                    <p class="price-text"><?php echo $offer["price"]; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            <?php } ?>
        </div>
    </div>
</section>


<section class="home-display mb-12 mt-12">
    <h1 class="home-category-title">Venez découvrir</h1>

    <div class="carousel-gen carouselBG" data-slides-visible="4" data-slides-to-scroll="2"
         data-slides-visible-mobile="2">
        <a href="/recherche?city=Bréhat">
            <div class="carousel-filter-card">
                <img src="/assets/images/homeCarouselImages/Brehat.jpeg" alt="Brehat"
                     style="width:100%;">
                <p class="carousel-filter-card-text">Bréhat</p>
            </div>
        </a>
        <a href="/recherche?city=Plouha">
            <div class="carousel-filter-card">
                <img src="/assets/images/homeCarouselImages/Plouha.jpg" alt="Plouha"
                     style="width:100%;">
                <p class="carousel-filter-card-text">Plouha</p>
            </div>
        </a>
        <a href="/recherche?city=Lannion">
            <div class="carousel-filter-card">
                <img src="/assets/images/homeCarouselImages/Lannion.jpg" alt="Lannion"
                     style="width:100%;">
                <p class="carousel-filter-card-text">Lannion</p>
            </div>
        </a>
        <a href="/recherche?city=Pléneuf">
            <div class="carousel-filter-card">
                <img src="/assets/images/homeCarouselImages/Pleneuf.jpg" alt="Pleneuf"
                     style="width:100%;">
                <p class="carousel-filter-card-text">Pléneuf</p>
            </div>
        </a>
        <a href="/recherche?city=Paimpol">
            <div class="carousel-filter-card">
                <img src="/assets/images/homeCarouselImages/Paimpol.jpg" alt="Paimpol"
                     style="width:100%;">
                <p class="carousel-filter-card-text">Paimpol</p>
            </div>
        </a>
        <a href="/recherche?city=Erquy">
            <div class="carousel-filter-card">
                <img src="/assets/images/homeCarouselImages/Erquy.jpg" alt="Erquy"
                     style="width:100%;">
                <p class="carousel-filter-card-text">Erquy</p>
            </div>
        </a>
        <a href="/recherche?city=Pontrieux">
            <div class="carousel-filter-card">
                <img src="/assets/images/homeCarouselImages/Pontrieux.jpg" alt="Pontrieux"
                     style="width:100%;">
                <p class="carousel-filter-card-text">Pontrieux</p>
            </div>
        </a>
        <a href="/recherche?city=Saint-Brieuc">
            <div class="carousel-filter-card">
                <img src="/assets/images/homeCarouselImages/Saint-Brieuc.webp" alt="Saint-Brieuc"
                     style="width:100%;">
                <p class="carousel-filter-card-text">Saint-Brieuc</p>
            </div>
        </a>
    </div>
</section>
