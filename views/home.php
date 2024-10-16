<?php
/** @var $this \app\core\View */
$this->title = "Home";

?>

<h1>Home </h1>
<h2>coucou <?php echo $name ?></h2>

<a href="/storybook">Storybook</a>
<x-slider color="blue" label="Prix" min="10" max="100" type="minmax"></x-slider>