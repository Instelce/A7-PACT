<?php
/** @var $this \app\core\View */
$this->title = "research";
?>
<main class="vertical-list">
    <?php
    // var_dump($offers);
    foreach ($offers as $offer) {
        ?>
    <a href="/offers/detail?id=<?php echo $offer["id"]; ?>">
        <x-search-page-card>
            <img slot="image" alt=<?php if ($offer["image"] == NULL) {
                    ?>"l offre ne contient pas d image" <?php
                } else {
                    ?>"photo d article" src="image" <?php //placer l'url de l'addresse
                } ?> />
            <span slot="title"><?php echo $offer["title"]; ?> </span>
            <span slot="author"><?php echo $offer["author"]; ?> </span>
            <span slot="type"><?php echo $offer["type"]; ?> </span>
            <span slot="price">À partir de <?php echo $offer["price"]; ?>€ </span>
            <span slot="location"><?php $offer["location"]; ?> </span>
            <span slot="locationDistance"><?php echo "pas loin"; ?> </span>
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