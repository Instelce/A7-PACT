<?php
/** @var $this \app\core\View */
$this->title = "Home";

?>

<a class="my-2" href="/storybook">Voir le storybook</a>

<h2 class="my-2">Coucou <?php echo $name ?>, tu as choisit le nombre <?php echo $value ?></h2>

<form action="" method="post" class="flex my-4 items-center gap-4">
    <x-select name="value">
        <span slot="trigger">Number</span>
        <div slot="options">
            <div data-value="1">1</div>
            <div data-value="2">2</div>
            <div data-value="3">3</div>
        </div>
    </x-select>
    <x-input rounded>
        <input slot="input" type="text" name="name" placeholder="Nom">
    </x-input>

    <button class="button">
        Changer
    </button>
</form>
