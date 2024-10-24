<?php

use app\core\Application;

$this->title = "Profile";

$user = Application::$app->user;
?>

<div class="flex items-center justify-center">
  <h1 class="heading-2">Profile de <span class="underline"><?php echo $user->mail ?></span></h1>
</div>
