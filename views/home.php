<?php
/** @var $this \app\core\View */
$this->title = "Home";

?>

<a class="my-2" href="/storybook">Voir le storybook</a>

<h2 class="my-2">Coucou <?php echo $name ?></h2>

<a href="/storybook">Storybook</a>
<h2 class="my-2">Coucou <?php echo $name ?></h2>
<form action="" method="post" class="flex my-4 items-center gap-4">
  <x-input>
    <input slot="input" type="text" name="name" placeholder="Nom">
  </x-input>
  <x-button type="submit">Changer</x-button>
</form>

<x-slider color="Blue" label="Prix" min="10" max="1000" type="minmax"></x-slider>