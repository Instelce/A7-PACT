<?php
/** @var $this \app\core\View */

use app\core\Application;

$this->title = "Home";
$this->cssFile = "home";
$this->jsFile = "home";
$this->waves = true;

// $this->waves = true;

?>

<main class="homeDisplay">
    <div class="homeDisplayDiv w-full">
        <h1 class="heading-1">
            Découvrez
            <div class="rotating-text-container">
                <span style="--i: 0">Lannion</span>
                <span style="--i: 1">Saint-Brieuc</span>
                <span style="--i: 2">Plouha</span>
                <span style="--i: 3">Paimpol</span>
                <span style="--i: 4">Bréhat</span>
            </div>
        </h1>

        <x-input rounded class="lg:w-[600px] sm:w-full">
            <input slot="input" type="text" placeholder="Recherchez des activités, visites, spectacles..."
                id="searchBar">
            <button slot="button" class="button only-icon sm" id="searchButton">
                <i data-lucide="search" stroke-width="2"></i>
            </button>
        </x-input>

        <div class="homeDisplayDiv">
            <h1 class="home-category-title">Destinations phares</h1>

            <div class="carousel-gen" data-slides-visible="3" data-slides-to-scroll="1">

                <?php foreach ($offersALaUne as $offer) { ?>
                    <a href="/offres/<?php echo $offer["id"]; ?>">

                        <div class="home-card">
                            <div class="image">
                                <img src="<?php echo $offer["image"]; ?>" alt="Image de <?php echo $offer['title']; ?>">
                            </div>

                            <div class="cardContent">
                                <h1><?php echo $offer["title"]; ?></h1>
                                <div class="enLigne">
                                    <p class="par">Par</p>
                                    <p class="name"><?php echo $offer["author"]; ?></p>
                                </div>

                                <div class="enLigneGap">
                                    <p><?php echo $offer["type"]; ?></p>
                                    <div class="enLigne">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round"
                                            stroke-linejoin="round" class="lucide lucide-map-pin">
                                            <path
                                                d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0" />
                                            <circle cx="12" cy="10" r="3" />
                                        </svg>
                                        <p><?php echo $offer["location"]; ?></p>
                                    </div>
                                </div>

                                <div class="enLigneGap">
                                    <div class="stars" data-number="<?php echo $offer["rating"] ?>">
                                    </div>
                                    <div class="enLigne">
                                        <p><?php echo $offer["ratingsCount"] ?></p>
                                        <p>Avis</p>
                                    </div>
                                </div>

                                <div class="summary">
                                    <p><?php echo $offer["summary"]; ?></p>
                                </div>

                            </div>
                            <div class="price">
                                <p class="price-text"><?php echo $offer["price"]; ?></p>
                            </div>
                        </div>
                    </a>
                <?php } ?>
            </div>
        </div>

</main>