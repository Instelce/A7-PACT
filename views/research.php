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
        <div id="popup" class="hidden absolute w-full h-full blur-sm z-0">
            <div class=" max-w-[500px] h-full bg-white z-10">
                <h1>zzzz</h1>
            </div>
        </div>
        </div>

        <!-- Offers, generated in js file -->
        <div class="flex flex-col gap-2">

            <?php foreach ($offers as $offer) { ?>
                <a href="/offres/<?php echo $offer["id"]; ?>">
                    <article class="research-card">
                        <div class="research-card--photo">
                            <?php if ($offer["image"] !== NULL) {
                                ?><img alt="photo d'article" src="<?php echo $offer["image"] ?>"/> <?php
                            } ?>
                        </div>

                        <div class="research-card--body">
                            <header>
                                <h2 class="research-card--title"><?php echo $offer["title"]; ?> </h2>
                                <p><?php echo $offer["type"]; ?> par <a href="/comptes/"
                                                                        class="underline"><?php echo $offer["author"]; ?></a>
                                </p>
                            </header>

                            <p><?php echo $offer["summary"]; ?></p>

                            <div class="flex gap-2 mt-auto pt-4">
                                <a href="" class="button gray w-full spaced">Itinéraire<i data-lucide="map"></i></a>
                                <a href="" class="button blue w-full spaced">Voir plus<i data-lucide="chevron-right"></i></a>
                            </div>
                        </div>
                    </article>
                </a>
            <?php } ?>
        </div>
    </main>
<?php
?>