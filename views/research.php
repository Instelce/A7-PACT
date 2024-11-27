<?php
/** @var $this \app\core\View */
/** @var $offers array */

$this->title = "Recherche";
$this->cssFile = "research";
$this->jsFile = "research";

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
?>
<!-- Search bar, sort and filter -->
<div class="flex flex-col mb-4">
    <x-input rounded>
        <input slot="input" type="text" placeholder="Rechercher">
        <button slot="button" class="button only-icon sm">
            <i data-lucide="search" stroke-width="2"></i>
        </button>
    </x-input>
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
    <div class="flex gap-2 w-full">
        <button id="filterButton" class="button gray w-full">
            <span>Plus de filtres</span>
            <i data-lucide="sliders-horizontal" class="w-[18px] h-[18px]"> </i>
        </button>
        <button class="button gray w-full">
            <span>A proximité</span>
            <i data-lucide="navigation" class="w-[18px] h-[18px]"> </i>
        </button>
    </div>
</div>
<!-- more filters-->
<div id="popup"
    class="hidden lg:fixed lg:inset-0 lg:bg-black/50 flex lg:justify-start justify-between items-start z-50">
    <div
        class="popup-content bg-white lg:rounded-lg lg:shadow-lg lg:max-w-[400px] w-full h-full mb-1 lg:mb-0 p-2 lg:p-6 lg:pt-[84px]">
        <!-- Contenu de la popup -->
        <!-- <x-input rounded>
                <input slot="input" type="text" placeholder="Rechercher" class="w-full">
                <button slot="button" class="button only-icon sm">
                    <i data-lucide="search" stroke-width="2"></i>
                </button>
            </x-input> -->

        <div>
            <!-- <div class="flex flex-col">
                <span class="text-base font-bold text-black">Intervalle de prix</span>
                <div class="h-px bg-zinc-400 mt-2"></div>
            </div> -->
            <div class="flex gap-2">
                <x-slider color="#C933E7" label="Prix" min="0" max="200" type="double"></x-slider>
            </div>
            <div class="flex gap-2">
                <x-slider color="#C933E7" label="note" min="0" max="5" type="double"></x-slider>
            </div>
            <div class="flex flex-row items-center gap-2 py-4">
                <div class="flex gap-2">
                    <input class="switch" type="checkbox" id="switchtest" />
                    <label class="switch" for="switchtest">Toggle</label>
                </div>
                <h3>Uniquement ouvert</h3>
            </div>
            <div class="w-full flex lg:flex-row gap-2">
                <x-input>
                    <input slot="input" type="date">
                </x-input>
                <x-input>
                    <input slot="input" type="date">
                </x-input>
            </div>
            <x-input>
                <input slot="input" type="text" placeholder="Ville">
            </x-input>
            <!--
                <x-select id="category" name="category" required>
                    <label slot="label">Catégorie</label>
                    <span slot="trigger">Choisir une categorie</span>
                    <div slot="options">
                        <div data-value="visit">Visite</div>
                        <div data-value="activity">Activité</div>
                        <div data-value="restaurant">Restaurant</div>
                        <div data-value="show">Spectacle</div>
                        <div data-value="attraction-parc">Parc d'attraction</div>
                    </div>
                </x-select>
                -->

        </div>
    </div>
    </div>

    <!-- Offers, generated in js file -->
    <div class="flex flex-col gap-2">
                    <div class="research-card--body">
                        <header>
                            <h2 class="research-card--title"><?php echo $offer["title"]; ?> </h2>
                            <p><?php echo $offer["type"]; ?> par <a href="/comptes/<?php echo $offer["professional_id"] ?>"
                                    class="underline"><?php echo $offer["author"]; ?></a>
                            </p>
                        </header>

                        <p class="summary"><?php echo $offer["summary"]; ?></p>

                        <div class="flex gap-2 mt-auto pt-4">
                            <a href="" class="button gray w-full spaced">Itinéraire<i data-lucide="map"></i></a>
                            <a href="" class="button blue w-full spaced">Voir plus<i data-lucide="chevron-right"></i></a>
                        </div>
                    </div>
                </article>
            </a>
    </div>
</div>
</div>

<!-- Offers, generated in js file -->
<div class="flex flex-col gap-2">


</div>
<?php
?>