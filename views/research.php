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
<main>
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
                <button class="category-item">
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
    <div id="popup" class="hidden fixed inset-0 bg-black/50 flex justify-center items-center z-50">
        <div class="popup-content bg-white rounded-lg shadow-lg max-w-[500px] w-full p-6">
            <!-- Contenu de la popup -->
            <x-input rounded>
                <input slot="input" type="text" placeholder="Rechercher" class="w-full">
                <button slot="button" class="button only-icon sm">
                    <i data-lucide="search" stroke-width="2"></i>
                </button>
            </x-input>

            <div class="mb-7">
                <div class="flex flex-col">
                    <span class="text-base font-bold text-black">Intervalle de prix</span>
                    <div class="h-px bg-zinc-400 mt-2"></div>
                </div>

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
    </div>

    <!-- Offers, generated in js file -->
    <div class="flex flex-col gap-2">


    </div>
</main>
<?php
?>