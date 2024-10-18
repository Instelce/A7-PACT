<?php

/** @var $exception \Exception */

?>

<h1 class="heading-1 mb-4">Oops! une pitite erreur</h1>

<h3 class="mb-2">Error code / <?php echo $exception->getCode() ?></h3>

<pre><?php echo $exception->getMessage() ?></pre>

<img src="/assets/images/errors/<?php echo rand(1, 4) ?>.gif" alt="Destroy"
     width="400" class="mt-10 hidden-anim">
