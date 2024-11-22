<?php
/** @var $this \app\core\View */

use app\core\Application;

$this->title = "Home";
$this->cssFile = "home-card";


?>

<?php if ($_ENV['APP_ENVIRONMENT'] === 'dev') { ?>
    <div class="flex flex-col gap-1">
        <a href="/offres/creation" class="link pro">Création d'une offre</a>
        <a href="/recherche" class="link">Recherche (liste des offres)</a>
        <a href="/dashboard/offres" class="link pro">Dashboard pro</a>
    </div>
<?php } else { ?>
    <?php Application::$app->response->redirect('/recherche'); ?>
<?php } ?>

<main>

    <?php
    foreach ($offersALaUne as $offer) {
        ?>
        <a href="/offres/<?php echo $offer["id"]; ?>">
            <div>

                <div class="home-card">
                    <div class="image">
                        <img src="<?php echo $offer["image"]; ?>" alt="Image de <?php echo $offer['title']; ?>">
                    </div>

                    <div class="cardContent">
                        <h1><?php echo $offer["title"]; ?></h1>
                        <div class="enLigne">
                            <p class="par">Par</p>
                            <p class="name"><?php echo $offer["author"]; ?></p>
                        </div>

                        <div class="enLigneGap">
                            <p><?php echo $offer["type"]; ?></p>
                            <div class="enLigne">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin">
                                    <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/>
                                    <circle cx="12" cy="10" r="3"/>
                                </svg>
                                <p><?php echo $offer["location"]; ?></p>
                            </div>
                        </div>

                        <div class="summary">
                            <p><?php echo $offer["summary"]; ?></p>
                        </div>

                        <div class="enLigneGap">
                            <div class="enLigne">
                                <p>232</p>
                                <p>Avis</p>
                            </div>
                        </div>

                        <div class="opposeLigne">
                            <div class="enLigne">
                                <p>Il y a</p>
                                <p><?php echo $offer["dateSincePublication"]; ?></p>
                                <p>j</p>
                            </div>

                            <div class="price">
                                <?php if (!empty($offer["price"])): ?>
                                    <p class="price-text"><?php echo $offer["price"]; ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
        <?php
    }
    ?>
</main>
<?php
?>
