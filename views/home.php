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
    <x-input placeholder="ce input n'est pas rounded" hasbutton="true" rounded="false">
        <i name="icon-left" data-lucide="search"></i>
    </x-input>
    <x-input placeholder="ce input est rounded" hasbutton="false" rounded="true">
        <i name="icon-right" data-lucide="circle-arrow-right"></i>
    </x-input>

</div>