<?php
/** @var $this \app\core\View */
$this->title = "Home";

?>

<h1>Home </h1>
<h2>coucou <?php echo $name ?></h2>

<div class="flex gap-2">
    <x-button>coucou</x-button>

    <x-button color="gray">
        <i name="icon-left" data-lucide="alarm-clock-minus"></i>
        coucou
    </x-button>
    <x-button color="danger">coucou</x-button>
    <x-button color="purple">coucou</x-button>
    <x-input placeholder="salut">
        <i name="icon-right" data-lucide="search"></i>
    </x-input>

</div>