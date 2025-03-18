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
<!-- Search bar, sort and filter, map -->
<div id="topPart" class="flex flex-col md:flex-row gap-1 flex-nowrap lg:gap-4 w-full md:h-[170px]">
    <div id="searchPart" class="flex flex-col w-full md:w-2/3">
        <div class="flex flex-row gap-1 lg:gap-2">
            <x-input rounded>
                <input class="search-input" slot="input" type="text" placeholder="Rechercher par nom d'offre"
                    title="Rechercher par nom d'offre">
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
            <button id="<?php echo strtolower($filterName); ?>" class="category-item BlockInteraction"
                title="Filtrer par <?php echo htmlentities($filterName); ?>">
                <i data-lucide="<?php echo $iconName ?>" class="h-[20px] w-[20px]"></i>
                <span><?php echo htmlentities($filterName); ?></span>
            </button>
            <?php
            }
            ?>
        </div>
    </div>
    <div id="mapContainer" class="relative w-0 h-0 md:w-full md:h-full">
        <div class="w-full flex flex-row justify-end absolute p-4">
            <i data-lucide="expand" class="w-8 h-8 hover:scale-105 hidden md:block cursor-pointer z-40"
                title="Agrandir la carte" id="fullScaleMap">
            </i>
            <i data-lucide="shrink" class="w-8 h-8 hover:scale-105 hidden md:hidden cursor-pointer z-40"
                title="Fermer la carte" id="closeMap">
            </i>
        </div>
        <div id="map" class="map w-full h-full z-10"></div>
    </div>
</div>
<!-- more filters-->
<div id="popup"
    class="close hidden lg:fixed lg:inset-0 lg:bg-white/50 flex lg:justify-start justify-between items-start z-50">
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
            <div class="w-full flex flex-row justify-end items-center">
                <div id="closeFilters" class="cursor-pointer" title="Fermer les filtres">
                    <svg fill="#000000" viewBox="0 0 32 32" width="32" height="32" xmlns="http://www.w3.org/2000/svg">
                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                        <g id="SVGRepo_iconCarrier">
                            <path
                                d="M18.8,16l5.5-5.5c0.8-0.8,0.8-2,0-2.8l0,0C24,7.3,23.5,7,23,7c-0.5,0-1,0.2-1.4,0.6L16,13.2l-5.5-5.5 c-0.8-0.8-2.1-0.8-2.8,0C7.3,8,7,8.5,7,9.1s0.2,1,0.6,1.4l5.5,5.5l-5.5,5.5C7.3,21.9,7,22.4,7,23c0,0.5,0.2,1,0.6,1.4 C8,24.8,8.5,25,9,25c0.5,0,1-0.2,1.4-0.6l5.5-5.5l5.5,5.5c0.8,0.8,2.1,0.8,2.8,0c0.8-0.8,0.8-2.1,0-2.8L18.8,16z">
                            </path>
                        </g>
                    </svg>
                </div>
            </div>
            <div>
                <h3 class="lg:block hidden section-header mb-2">Tris</h3>
                <div class="BlockInteraction">
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
                            <div data-value="decroissantRating">Notes décroissantes <svg
                                    xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-arrow-down-wide-narrow">
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
            </div>
            <div>
                <h3 class="lg:block hidden section-header mb-2">Restaurant</h3>
                <div class="BlockInteraction">
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
            <button class="button gray w-full BlockInteraction" id="aProximite">
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