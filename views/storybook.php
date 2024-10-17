<?php
/** @var $this \app\core\View */

$this->title = "Storybook";
$this->cssFile = "storybook";
?>

<div class="main-container">
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
                <button class="button">Button</button>
                <button class="button purple">Button</button>
                <button class="button danger">Button</button>
                <button class="button gray">Button</button>
            </div>

            <div class="flex gap-2">
                <button class="button icon-left">
                    <i data-lucide="plus"></i>
                    Button
                </button>
                <button class="button icon-right">
                    Button
                    <i data-lucide="plus"></i>
                </button>
                <button class="button only-icon">
                    <i data-lucide="plus"></i>
                </button>
            </div>

            <div class="flex gap-2">
                <button class="button lg">
                    Button
                </button>
                <button class="button md">
                    Button
                </button>
                <button class="button sm">
                    Button
                </button>
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
                    <i slot="icon-right" data-lucide="send"></i>
                    <input slot="input" type="text" placeholder="Placeholder">
                </x-input>
            </div>

            <div class="flex gap-2">
                <x-input placeholder="Placeholder" rounded>
                    <input slot="input" type="text" placeholder="Placeholder">
                    <button slot="button" class="button only-icon sm">
                        <i data-lucide="search"></i>
                    </button>
                </x-input>
                <x-input placeholder="Search" rounded>
                    <input slot="input" type="text" placeholder="Placeholder">
                    <button slot="button" class="button sm">
                        Search
                    </button>
                </x-input>
            </div>

            <div class="flex gap-2">
                <x-input>
                    <input slot="input" type="text" placeholder="Placeholder">
                    <p slot="helper">Helper</p>
                </x-input>
                <x-input>
                    <input slot="input" type="text" placeholder="Placeholder">
                    <p slot="error">Erreur</p>
                </x-input>
                <x-input>
                    <input slot="input" type="text" placeholder="Placeholder">
                    <p slot="helper">Helper</p>
                    <p slot="error">Erreur</p>
                </x-input>
            </div>

            <div class="flex gap-2">
                <x-input>
                    <p slot="label">Label</p>
                    <input slot="input" id="toto" type="text" placeholder="Placeholder" required>
                    <p slot="helper">Helper</p>
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

                <label>
                    <input type="radio" name="test">
                    gougou
                </label>
                <label>
                    <input type="radio" name="test">
                    gougou
                </label>

            </div>

            <div class="flex gap-2">

                <label>
                    <input type="checkbox" id="bla">
                    blablabla
                </label>
                <label>
                    <input type="checkbox" id="bla">
                    blablabla
                </label>

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
                <x-slider color="#0057FF" label="Prix" min="0" max="238" type="minmax"></x-slider>
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
            <h2>Select</h2>
        </header>

        <div class="flex flex-col gap-4">
            <div class="flex gap-2">
              <x-select>
                <span slot="trigger">Select</span>
                <div slot="options">
                  <div class="option" data-value="value 1">Option 1</div>
                  <div class="option" data-value="value 2">Option 2</div>
                  <div class="option" data-value="value 3">Option 3</div>
                  <div class="option" data-value="value 4">Option 4</div>
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

<section class="flex flex-col gap-4">

    <header>
        <h2>Search Page Card</h2>
    </header>

    <div class="flex gap-2">
        <x-search-page-card>
            <img slot="image" src="/assets/images/brehat.jpeg" alt="Brehat">
            <span slot="title">Balade familiale à vélo "Qui m’aime me suive"</span>
            <span slot="author">Jean Bergeron</span>
            <span slot="type">Activité</span>
            <span slot="price">À partir de 0€</span>
            <span slot="location">Bréhat</span>
            <span slot="locationDistance">À 15 min de chez vous</span>
            <span slot="date">Il y a 1 j</span>
        </x-search-page-card>

    </div>

    <div class="flex gap-2">
        <x-search-page-card>
            <img slot="image" src="/assets/images/7iles.jpeg" alt="7iles">
            <span slot="title">Excursion vers les 7 Iles</span>
            <span slot="author">Alice Martin</span>
            <span slot="type">Visite</span>
            <span slot="price">Dès 21.50 € / personne</span>
            <span slot="location">Perros-Guirec</span>
            <span slot="locationDistance">À 1h de chez vous</span>
            <span slot="date">Il y a 7j</span>
        </x-search-page-card>
    </div>

</section>

</div>