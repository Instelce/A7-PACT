<?php
/** @var $this \app\core\View */
$this->title = "Home";

?>

<h1>Home </h1>
<h2>coucou <?php echo $name ?></h2>

<div class="flex gap-2">
    <x-button>coucou</x-button>

    <x-button color="gray">
        <i slot="icon-right" data-lucide="alarm-clock-minus"></i>
        coucou
    </x-button>
    <x-button color="danger">coucou</x-button>
    <x-button color="purple">coucou</x-button>
    <x-input placeholder="ce input n'est pas rounded" hasbutton="false" txtbutton="search" rounded="false">
        <i slot="icon-left" data-lucide="search"></i>
    </x-input>
    <x-input placeholder="ce input est rounded" rounded="true">
        <i slot="icon-right" data-lucide="circle-arrow-right"></i>
    </x-input>
    <x-slider label="Prix" type="minmax"></x-slider>
    <x-checkbox labelTexte="WAZAA">WAZAA</x-checkbox>

</div>