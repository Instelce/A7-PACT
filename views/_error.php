<?php

/** @var $exception \Exception */

use app\core\Application;

$this->title = 'Oops';

?>

<?php if ($_ENV['APP_ENVIRONMENT'] === 'dev') { ?>
    <h1 class="heading-1 mb-4">Oops! une pitite erreur</h1>

    <h3 class="mb-2">Error code / <?php echo $exception->getCode() ?></h3>

    <pre><?php echo $exception->getMessage() ?></pre>

    <img src="/assets/images/errors/<?php echo rand(1, 20) ?>.gif" alt="Destroy"
         width="400" class="mt-10 hidden-anim">
<?php } else { ?>
    <?php if ($exception->getCode() === 404) { ?>
        <h1 class="heading-1 mb-4">La page que vous cherchez n'existe pas</h1>
    <?php } else if ($exception->getCode() === 403) { ?>
        <h1 class="heading-1 mb-4">Vous n'avez pas les droits pour accéder à cette page</h1>
    <?php } else { ?>
        <h1 class="heading-1 mb-4">Une erreur est survenue</h1>
    <?php } ?>

    <?php if (Application::$app->isAuthenticated()) { ?>
        <p>Retourner sur <a href="/dashboard" class="link">votre tableau de bord</a>.</p>
    <?php } else { ?>
        <p>Retourner à la <a href="/" class="link">page d'accueil</a>.</p>
    <?php } ?>
<?php } ?>
