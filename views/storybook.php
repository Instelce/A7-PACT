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

        <div class="flex flex-col gap-4">
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
                <x-button icon>
                    <i data-lucide="plus"></i>
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

        <div class="flex flex-col gap-4">
            <div class="flex gap-2">
                <x-input>
                    <input slot="input" type="text" placeholder="Placeholder">
                </x-input>
                <x-input rounded>
                    <input slot="input" type="text" placeholder="Placeholder">
                </x-input>
            </div>

            <div class="flex gap-2">
                <x-input>
                    <i slot="icon-left" data-lucide="search"></i>
                    <input slot="input" type="text" placeholder="Placeholder">
                </x-input>
                <x-input placeholder="Placeholder">
                    <i slot="icon-right" data-lucide="search"></i>
                    <input slot="input" type="text" placeholder="Placeholder">
                </x-input>
            </div>

            <div class="flex gap-2">
                <x-input placeholder="Placeholder" rounded>
                    <input slot="input" type="text" placeholder="Placeholder">
                    <x-button slot="button" size="sm" icon>
                        <i data-lucide="search"></i>
                    </x-button>
                </x-input>
                <x-input placeholder="Search" rounded>
                    <input slot="input" type="text" placeholder="Placeholder">
                    <x-button slot="button" size="sm">
                        Search
                    </x-button>
                </x-input>
            </div>
        </div>

    </section>

    <section>
        <header>
            <h2>Checkbox</h2>
        </header>

        <div class="flex flex-col gap-4">
            <div class="flex gap-2">
            </div>

            <div class="flex gap-2">
            </div>

            <div class="flex gap-2">
            </div>
        </div>

    </section>

    <section>
        <header>
            <h2>Slider</h2>
        </header>

        <div class="flex flex-col gap-4">
            <div class="flex gap-2">
            </div>

            <div class="flex gap-2">
            </div>

            <div class="flex gap-2">
            </div>
        </div>

    </section>

    <section>
        <header>
            <h2>Toggle</h2>
        </header>

        <div class="flex flex-col gap-4">
            <div class="flex gap-2">
            </div>

            <div class="flex gap-2">
            </div>

            <div class="flex gap-2">
            </div>
        </div>

    </section>

    <section>
        <header>
            <h2>Toggle</h2>
        </header>

        <div class="flex flex-col gap-4">
            <div class="flex gap-2">
              <x-select>
                <span slot="trigger">Select</span>
                <div slot="options">
                  <div>Option 1</div>
                  <div>Option 2</div>
                  <div>Option 3</div>
                  <div>Option 4</div>
                </div>
              </x-select>
            </div>

            <div class="flex gap-2">
            </div>

            <div class="flex gap-2">
            </div>
        </div>
    </section>
</div>

