<?php
/** @var $this \app\core\View */
/** @var $researchInfo array */

$maxPrice = intval($researchInfo["MaxMinimumPrice"]);

$this->title = "Recherche";
$this->cssFile = "research";
$this->jsFile = "research";
$this->leaflet = true;



$filtersNames = [
    "Spectacles",
    "Restauration",
    "Visites",
    "Activités",
    "Attractions",
];
$iconsNames = [
    "ticket",
    "utensils-crossed",
    "map-pin-house",
    "bike",
    "ferris-wheel",
];

// Get the class in function of the status




?>
<div class="wave hidden md:block">
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
    <svg class="waveSvg2" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
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
<!-- Search bar, sort and filter -->
<div class="flex flex-col">
    <div class="flex flex-row gap-1 lg:gap-2">
        <x-input rounded>
            <input class="search-input" slot="input" type="text" placeholder="Rechercher par nom d'offre">
            <button slot="button" id="filterButton" class="button gray p-4" title="Filtrer les offres">
                <i data-lucide="sliders-horizontal" class="w-[18px] h-[18px]"> </i>
            </button>
            <button slot="button" id="searchMap" class="button gray p-4" title="Afficher la carte">
                <i data-lucide="map" class="w-[18px] h-[18px]"> </i>
            </button>
        </x-input>
    </div>
    <div class="categories-container">
        <?php
        foreach ($filtersNames as $key => $filterName) {
            $iconName = $iconsNames[$key];
            ?>
            <button id="<?php echo strtolower($filterName); ?>" class="category-item">
                <i data-lucide="<?php echo $iconName ?>" class="h-[20px] w-[20px]"></i>
                <span><?php echo htmlentities($filterName); ?></span>
            </button>
            <?php
        }
        ?>
    </div>
</div>
<!-- more filters-->
<div id="popup"
    class="close hidden lg:fixed lg:inset-0 lg:bg-black/50 flex lg:justify-start justify-between items-start z-40">
    <div
        class="popup-content bg-white lg:rounded-lg lg:shadow-lg lg:max-w-[400px] w-full h-full lg:mb-0 p-2 lg:p-6 lg:pt-[84px] flex flex-row justify-start items-start overflow-y-scroll">
        <!-- Contenu de la popup -->
        <!-- <x-input rounded>
                <input slot="input" type="text" placeholder="Rechercher" class="w-full">
                <button slot="button" class="button only-icon sm">
                    <i data-lucide="search" stroke-width="2"></i>
                </button>
            </x-input> -->

        <div class="flex flex-col gap-1 lg:gap-4 w-full">

            <div>
                <h3 class="lg:block hidden section-header mb-2">Tris</h3>

                <x-select id="sort">
                    <span slot="trigger">Tris</span>
                    <div slot="options">
                        <div data-value="croissantPrice">
                            Prix croissant
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-arrow-down-narrow-wide">
                                <path d="m3 16 4 4 4-4" />
                                <path d="M7 20V4" />
                                <path d="M11 4h4" />
                                <path d="M11 8h7" />
                                <path d="M11 12h10" />
                            </svg>
                        </div>
                        <div data-value="decroissantPrice">Prix décroissant <svg xmlns="http://www.w3.org/2000/svg"
                                width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-arrow-down-wide-narrow">
                                <path d="m3 16 4 4 4-4" />
                                <path d="M7 20V4" />
                                <path d="M11 4h10" />
                                <path d="M11 8h7" />
                                <path d="M11 12h4" />
                            </svg></div>

                        <div data-value="croissantRating">Notes croissantes <svg xmlns="http://www.w3.org/2000/svg"
                                width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-arrow-down-narrow-wide">
                                <path d="m3 16 4 4 4-4" />
                                <path d="M7 20V4" />
                                <path d="M11 4h4" />
                                <path d="M11 8h7" />
                                <path d="M11 12h10" />
                            </svg></div>
                        <div data-value="decroissantRating">Notes décroissantes <svg xmlns="http://www.w3.org/2000/svg"
                                width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-arrow-down-wide-narrow">
                                <path d="m3 16 4 4 4-4" />
                                <path d="M7 20V4" />
                                <path d="M11 4h10" />
                                <path d="M11 8h7" />
                                <path d="M11 12h4" />
                            </svg>
                        </div>
                        <div data-value="reset">Aucun Tri</div>
                    </div>
                </x-select>
            </div>
            <div>
                <h3 class="lg:block hidden section-header mb-2">Restaurant</h3>
                <x-select id="filterRangePriceRestau">
                    <span slot="trigger">Gamme de prix <span class="lg:hidden">du restaurant</span></span>
                    <div slot="options">
                        <div data-value="1" class="selected">€ (Moins de 25 €)</div>
                        <div data-value="2">€€ (Entre 25 et 40€)</div>
                        <div data-value="3">€€€ (Plus de 40€)</div>
                        <div data-value="reset">Toute les Gammes de prix</div>
                    </div>
                </x-select>
            </div>
            <div>
                <h3 class="lg:block hidden section-header mb-2">Intervalle de prix</h3>

                <div class="flex gap-2 w-full">
                    <x-slider class="w-full" id="slider-price" color="#0057FF" label="Prix" min="0"
                        max="<?php echo $maxPrice ?>" type="double"></x-slider>
                </div>
            </div>

            <div>
                <h3 class="lg:block hidden section-header mb-2">Note minimale</h3>

                <div class="flex gap-2 w-full">
                    <x-slider class="w-full" id="slider-rating" color="#0057FF" label="Note" min="0" max="5" type="">
                    </x-slider>
                </div>
            </div>

            <div>
                <h3 class="lg:block hidden section-header mb-2">Ville</h3>

                <x-input>
                    <input slot="input" type="text" placeholder="Ville" class="searchCity">
                </x-input>
            </div>
            <button class="button gray w-full" id="aProximite">
                <span>A proximité</span>
                <i data-lucide="navigation" id="proximiteIcon" class="w-[18px] h-[18px]"> </i>
                <svg class="animate-spin hidden" id="proximiteLoader" xmlns="http://www.w3.org/2000/svg" width="0.8rem"
                    height="0.8rem" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"
                    stroke-linecap="round" stroke-linejoin="round" class="lucide ">
                    <path d="M21 12a9 9 0 1 1-6.219-8.56" />
                </svg>
            </button>
        </div>
    </div>
</div>
</div>
</div>
<!-- map -->
<div id="mapContainer" class="md:fixed md:bottom-[1vw] md:right-[1vw] z-50 flex flex-col items-start rounded-lg">
    <div class="w-full bg-white rounded-t-lg">
        <svg viewBox="0 0 24 24" fill="none" width="32" height="32" xmlns="http://www.w3.org/2000/svg" id="fullScaleMap"
            class="hidden md:block cursor-pointer">
            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
            <g id="SVGRepo_iconCarrier">
                <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M3 4C3 3.44772 3.44772 3 4 3H8C8.55228 3 9 3.44772 9 4C9 4.55228 8.55228 5 8 5H6.41421L9.70711 8.29289C10.0976 8.68342 10.0976 9.31658 9.70711 9.70711C9.31658 10.0976 8.68342 10.0976 8.29289 9.70711L5 6.41421V8C5 8.55228 4.55228 9 4 9C3.44772 9 3 8.55228 3 8V4ZM16 3H20C20.5523 3 21 3.44772 21 4V8C21 8.55228 20.5523 9 20 9C19.4477 9 19 8.55228 19 8V6.41421L15.7071 9.70711C15.3166 10.0976 14.6834 10.0976 14.2929 9.70711C13.9024 9.31658 13.9024 8.68342 14.2929 8.29289L17.5858 5H16C15.4477 5 15 4.55228 15 4C15 3.44772 15.4477 3 16 3ZM9.70711 14.2929C10.0976 14.6834 10.0976 15.3166 9.70711 15.7071L6.41421 19H8C8.55228 19 9 19.4477 9 20C9 20.5523 8.55228 21 8 21H4C3.44772 21 3 20.5523 3 20V16C3 15.4477 3.44772 15 4 15C4.55228 15 5 15.4477 5 16V17.5858L8.29289 14.2929C8.68342 13.9024 9.31658 13.9024 9.70711 14.2929ZM14.2929 14.2929C14.6834 13.9024 15.3166 13.9024 15.7071 14.2929L19 17.5858V16C19 15.4477 19.4477 15 20 15C20.5523 15 21 15.4477 21 16V20C21 20.5523 20.5523 21 20 21H16C15.4477 21 15 20.5523 15 20C15 19.4477 15.4477 19 16 19H17.5858L14.2929 15.7071C13.9024 15.3166 13.9024 14.6834 14.2929 14.2929Z"
                    fill="#000000"></path>
            </g>
        </svg>
        <svg fill="#000000" viewBox="0 0 32 32" width="32" height="32" xmlns="http://www.w3.org/2000/svg" id="closeMap"
            class="hidden md:hidden cursor-pointer">
            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
            <g id="SVGRepo_iconCarrier">
                <path
                    d="M18.8,16l5.5-5.5c0.8-0.8,0.8-2,0-2.8l0,0C24,7.3,23.5,7,23,7c-0.5,0-1,0.2-1.4,0.6L16,13.2l-5.5-5.5 c-0.8-0.8-2.1-0.8-2.8,0C7.3,8,7,8.5,7,9.1s0.2,1,0.6,1.4l5.5,5.5l-5.5,5.5C7.3,21.9,7,22.4,7,23c0,0.5,0.2,1,0.6,1.4 C8,24.8,8.5,25,9,25c0.5,0,1-0.2,1.4-0.6l5.5-5.5l5.5,5.5c0.8,0.8,2.1,0.8,2.8,0c0.8-0.8,0.8-2.1,0-2.8L18.8,16z">
                </path>
            </g>
        </svg>
    </div>
    <div id="map" class="map w-0 h-0 md:w-[13vw] md:h-[13vw] xl:w-[18vw] xl:h-[18vw] all-transition duration-500"></div>
</div>
<!-- Offers, generated in js file -->
<div class="flex flex-col gap-2 mt-4">
</div>
<div class="no-offers-message hidden">
    <h2>Aucune offre trouvée</h2>
    <p>Désolé, nous n'avons trouvé aucune offre correspondant à vos critères de recherche.</p>
    <p>Veuillez essayer d'ajuster vos filtres ou revenir plus tard pour voir les nouvelles opportunités disponibles.</p>
</div>
<div id="loader-section"></div>
<article class="w-full h-64 bg-gray-200 rounded-xl flex flex-col lg:flex-row gap-4 p-4 hidden" id="loaderLoaderSection">
    <div class="w-full h-full bg-gray-300 animate-pulse rounded-xl"></div>
    <div class="w-full h-full flex flex-col p-4 justify-between">
        <div class="w-full h-full flex flex-col gap-4">
            <div class="w-full h-8 bg-gray-300 animate-pulse rounded-xl"></div>
            <div class="w-full h-6 bg-gray-300 animate-pulse rounded-xl"></div>
            <div class="w-full h-full flex flex-col gap-2">
                <div class="w-full h-4 bg-gray-300 animate-pulse rounded-xl"></div>
                <div class="w-full h-4 bg-gray-300 animate-pulse rounded-xl"></div>
                <div class="flex flex-row gap-4">
                    <div class="w-full h-4 bg-gray-300 animate-pulse rounded-xl"></div>
                    <div class="w-full h-4 bg-gray-300 animate-pulse rounded-xl"></div>
                    <div class="w-full h-4 bg-gray-300 animate-pulse rounded-xl"></div>
                </div>
            </div>
        </div>
        <div class="w-full h-full flex flex-row gap-4">
            <div class="w-full h-12 bg-gray-300 animate-pulse rounded-xl"></div>
            <div class="w-full h-12 bg-gray-300 animate-pulse rounded-xl"></div>
        </div>
    </div>
</article>
<?php
?>