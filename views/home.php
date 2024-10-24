<?php
/** @var $this \app\core\View */

use app\core\Application;

$this->title = "Home";

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
