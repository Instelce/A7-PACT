<?php
/** @var $this \app\core\View */

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
<main class="vertical-list">
    <!-- Search bar, sort and filter -->
    <div>
        <x-input rounded>
            <input slot="input" type="text" placeholder="Placeholder">
            <button slot="button" class="button only-icon sm">
                <i data-lucide="search"></i>
            </button>
        </x-input>
        <div class="filters-container">
            <?php
            foreach ($filtersNames as $key => $filterName) {
                $iconName = $iconsNames[$key];
                ?>
                <button class="filter-item">
                    <i data-lucide="<?php echo $iconName ?>" class="h-[20px] w-[20px]"></i>
                    <span><?php echo htmlentities($filterName); ?></span>
                </button>
                <?php
            }
            ?>
        </div>
        <div class="h-[39px] justify-start items-start gap-2.5 inline-flex mb-[45px] w-full">
            <button onclick="showPopup()" class="grow shrink basis-0 px-5 py-2.5 rounded-full border border-solid border-[#bbbbbb] justify-between
    items-center flex w-full">
                <span>Plus de filtres</span>
                <i data-lucide="sliders-horizontal" class="w-[18px] h-[18px]"> </i>
            </button>
            <button class="grow shrink basis-0 px-5 py-2.5 rounded-full border border-solid border-[#bbbbbb] justify-between
                items-center flex w-full">
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
    <!-- Offers -->
    <?php
    foreach ($offers as $offer) {
        ?>
        <a href="/offres/<?php echo $offer["id"]; ?>">
            <x-search-page-card>
                <?php if ($offer["image"] == NULL) {
                    ?><img slot="image" alt="l'offre ne contient pas d'image" /> <?php
                } else {
                    ?><img slot="image" alt="photo d'article" src="<?php echo $offer["image"] ?>" /> <?php
                } ?>

                <span slot="title"><?php echo $offer["title"]; ?> </span>
                <span slot="author"><?php echo $offer["author"]; ?> </span>
                <span slot="type"><?php echo $offer["type"]; ?> </span>
                <?php if ($offer["info"] > 0) { ?>
                    <span slot="info"><?php echo $offer["info"]; ?></span>
                <?php } ?>
                <span slot="location"><?php echo $offer["location"]; ?> </span>
                <!-- <span slot="locationDistance"> • <?php //show the distance between location and user position ?> </span> -->
            </x-search-page-card>
        </a>
        <?php
    }
    ?>
</main>
<?php
?>