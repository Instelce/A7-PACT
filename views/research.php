<?php
/** @var $this \app\core\View */

$this->title = "Recherche";
$this->cssFile = "research";

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
        <div
            class="h-[70px justify-start items-start gap-2.5 inline-flex mb-[25px] mt-[25px] sm:horizontal-scroll w-full">

            <?php
            foreach ($filtersNames as $key => $filterName) {
                $iconName = $iconsNames[$key];
                ?>
                <div
                    class="w-[100px] h-[70px] rounded-[20px] border border-[#bbbbbb] flex-col justify-center items-center gap-[3px] inline-flex border-solid w-full">
                    <i data-lucide="<?php echo $iconName ?>" class=" h-[20px] w-[20px] "></i>
                    <span>
                        <?php echo $filterName; ?>
                    </span>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
    <?php
    foreach ($offers as $offer) {//for each offer show, the composant x-search-page-card with a link to the detail offer
        ?>
        <a href="/offres/<?php echo $offer["id"]; ?>">
            <x-search-page-card>
                <?php if ($offer["image"] == NULL) {
                    ?><img slot="image" alt="l offre ne contient pas d image" /> <?php //no image for the offer
                } else {
                    ?><img slot="image" alt="photo d article" src="<?php echo $offer["image"] ?>" /> <?php //image of the offer
                } ?>

                <span slot="title"><?php echo $offer["title"]; ?> </span>
                <span slot="author"><?php echo $offer["author"]; ?> </span>
                <span slot="type"><?php echo $offer["type"]; ?> </span>
                <?php if ($offer["info"] > 0) { ?>
                    <span slot="info"><?php echo $offer["info"]; ?></span>
                <?php } ?>
                <span slot="location"><?php echo $offer["location"]; ?> </span>
                <!-- <span slot="locationDistance"> • <?php //show the distance between location and user position ?> </span> -->
                <span slot="date"> • Il y a <?php
                $date = new DateTime($offer["date"]);
                $now = new DateTime();
                $interval = $date->diff($now);

                if ($interval->y > 0) {
                    echo $interval->y . ' ' . ($interval->y > 1 ? 'ans' : 'an');
                } elseif ($interval->m > 0) {
                    echo $interval->m . ' ' . ($interval->m > 1 ? 'mois' : 'mois');
                } elseif ($interval->d >= 7) {
                    $weeks = floor($interval->d / 7);
                    echo $weeks . ' ' . ($weeks > 1 ? 'semaines' : 'semaine');
                } elseif ($interval->d > 0) {
                    echo $interval->d . ' ' . ($interval->d > 1 ? 'jours' : 'jour');
                } else {
                    echo 'mois de 24h';
                }
                ?>
                </span>
            </x-search-page-card>
        </a>
        <?php
    }
    ?>
</main>
<?php
?>