<?php
/** @var $this \app\core\View */

$this->title = "Storybook";
$this->cssFile = "storybook";
?>

<div class="container">
    <header>
        <h1>Storybook</h1>
        <p>Documentation of all our components.</p>
    </header>

    <section>
        <header>
            <h2>Button</h2>
        </header>

        <div class="flex flex-col gap-2">
            <div class="flex gap-2">
                <x-button>Button</x-button>
                <x-button color="purple">Button</x-button>
                <x-button color="danger">Button</x-button>
                <x-button color="gray">Button</x-button>
            </div>

            <div class="flex gap-2">
                <x-button>
                    <i slot="icon-left" data-lucide="plus"></i>
                    Button
                </x-button>
                <x-button>
                    <i slot="icon-right" data-lucide="plus"></i>
                    Button
                </x-button>
            </div>

            <div class="flex gap-2">
                <x-button size="lg">
                    Button
                </x-button>
                <x-button size="md">
                    Button
                </x-button>
                <x-button size="sm">
                    Button
                </x-button>
            </div>
        </div>

    </section>

    <section>
        <header>
            <h2>Input</h2>
        </header>

        <div class="flex flex-col gap-2">
            <div class="flex gap-2">
                <x-input placeholder="Placeholder"></x-input>
                <x-input placeholder="Placeholder" rounded></x-input>
            </div>

            <div class="flex gap-2">
                <x-input placeholder="Placeholder">
                    <i slot="icon-left" data-lucide="search"></i>
                </x-input>
                <x-input placeholder="Placeholder">
                    <i slot="icon-right" data-lucide="search"></i>
                </x-input>
            </div>

            <div class="flex gap-2">
                <x-input placeholder="Placeholder" rounded>
                    <x-button slot="button" size="sm">
                        <i data-lucide="search"></i>
                    </x-button>
                </x-input>
            </div>
        </div>

    </section>

</div>