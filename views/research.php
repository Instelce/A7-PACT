<?php
/** @var $this \app\core\View */

$this->title = "Recherche";
$this->cssFile = "research";

?>
<main class="vertical-list">
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
                <?php if ($offer["price"] > 0) { ?>
                    <span slot="price">À partir de <?php echo $offer["price"]; ?>€ </span>
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