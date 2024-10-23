<?php
/** @var $this \app\core\View */
$this->title = "research";
?>
<main class="vertical-list">
    <?php
    var_dump($offers);
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
                <span slot="price">À partir de <?php echo $offer["price"]; ?>€ </span>
                <span slot="location"><?php echo $offer["location"]; ?> </span>
                <!-- <span slot="locationDistance"><?php //show the distance between location and user position ?> </span> -->
                <span slot="date"><?php echo $offer["date"]; ?> </span>
            </x-search-page-card>
        </a>
        <hr class="horizontal-line">
        <?php
    }
    ?>
</main>
<?php
?>