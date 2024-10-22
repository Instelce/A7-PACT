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
                <x-input>
                    <i slot="icon-right" data-lucide="send"></i>
                    <input slot="input" type="text" placeholder="Placeholder">
                </x-input>
            </div>

            <div class="flex gap-2">
                <x-input rounded>
                    <input slot="input" type="text" placeholder="Placeholder">
                    <button slot="button" class="button only-icon sm">
                        <i data-lucide="search"></i>
                    </button>
                </x-input>
                <x-input rounded>
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

            <div class="flex gap-2">
                <x-input>
                    <p slot="label">Label</p>
                    <textarea slot="input" id="toto" type="text" placeholder="Placeholder" required></textarea>
                    <p slot="helper">Helper</p>
                </x-input>
            </div>
        </div>

    </section>

    <section>
        <header>
            <h2>Autocomplete input</h2>
        </header>

        <div class="flex gap-2">
            <x-input>
                <p slot="label">Label</p>
                <input slot="input" id="toto" type="text" placeholder="Placeholder" required>
                <div slot="list">
                    <div>Orange</div>
                    <div>Apple</div>
                    <div>Banana</div>
                </div>
            </x-input>
        </div>
    </section>

    <section>
        <header>
            <h2>Checkbox</h2>
        </header>

        <div class="flex flex-col gap-4">
            <div class="flex gap-2">
                <input class="checkbox" type="checkbox" id="bla">
                <label class="checkbox">blablabla</label>
                <input class="checkbox" type="checkbox" id="bla">
                <label class="checkbox">blablabla</label>
            </div>

            <div class="flex gap-2">
                <input class="checkboxNormal" type="radio" id="tezt" name="test">
                <label class="checkbox" for="tezt">gougou</label>
                <input class="checkboxNormal" type="radio" id="tezt" name="test">
                <label class="checkbox" for="tezt">gougou</label>
            </div>

            <div class="flex gap-2">
            </div>
        </div>

    </section>

    <section>
        <header>
            <h2>Section Header</h2>
        </header>

        <div class="flex flex-col gap-4">
            <div class="flex gap-2">
                <h2 class="section-header">c'est un test !</h2>
            </div>
        </div>

    </section>

    <section>
        <header>
            <h2>Slider</h2>
        </header>

        <div class="flex flex-col gap-4">
            <div class="flex gap-2">
                <x-slider color="#0057FF" label="Prix" min="0" max="55" type=""></x-slider>

            </div>

            <div class="flex gap-2">
                <x-slider color="#C933E7" label="Prix" min="0" max="200" type="double"></x-slider>
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
                <input class="switch" type="checkbox" id="switchtest" />
                <label class="switch" for="switchtest">Toggle</label>
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
                        <div data-value="value 1">Option 1</div>
                        <div data-value="value 2">Option 2</div>
                        <div data-value="value 3">Option 3</div>
                        <div data-value="value 4">Option 4</div>
                    </div>
                </x-select>
            </div>

            <div class="flex gap-2">
            </div>

            <div class="flex gap-2">
            </div>
        </div>
    </section>

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

    <section>
        <header>
            <h2>Tabs</h2>
        </header>

        <div class="flex flex-col gap-4">
            <div class="flex gap-2">
                <x-tabs>

                    <x-tab role="heading" slot="tab">
                        Tab 1
                    </x-tab>
                    <x-tab-panel role="region" slot="panel">
                        <p>Content 1</p>
                    </x-tab-panel>

                    <x-tab role="heading" slot="tab">Tab 2</x-tab>
                    <x-tab-panel role="region" slot="panel">
                        <p>Content 2</p>
                    </x-tab-panel>

                    <x-tab role="heading" slot="tab">Tab 3</x-tab>
                    <x-tab-panel role="region" slot="panel">
                        <p>Content 3</p>
                    </x-tab-panel>

                </x-tabs>
            </div>

            <div class="flex gap-2">
                <x-tabs class="column">

                    <x-tab role="heading" slot="tab">
                        <i data-lucide="user"></i>
                        Tab 1
                    </x-tab>
                    <x-tab-panel role="region" slot="panel">
                        <p>Content 1</p>
                    </x-tab-panel>

                    <x-tab role="heading" slot="tab">
                        <i data-lucide="euro"></i>
                        Tab 2
                    </x-tab>
                    <x-tab-panel role="region" slot="panel">
                        <p>Content 2</p>
                    </x-tab-panel>

                    <x-tab role="heading" slot="tab">
                        <i data-lucide="key"></i>
                        Tab 3
                    </x-tab>
                    <x-tab-panel role="region" slot="panel">
                        <p>Content 3</p>
                    </x-tab-panel>

                </x-tabs>
            </div>

            <div class="flex gap-2">
            </div>
        </div>
    </section>

    <section class="flex flex-col gap-4">
        <header>
            <h2>Acordeon</h2>
        </header>

        <div class="flex flex-col gap-4">
            <div class="flex gap-2">
                <x-acordeon text="Acordeon">
                    <div slot="content">
                        <p>Bravo vous avez réussie a ouvrir l'accordeon ! maintenant vous pouvez le fermez.</p>
                    </div>
                </x-acordeon>
            </div>
        </div>
    </section>


    <section class="flex flex-col gap-4">
        <header>
            <h2>Carousel</h2>
        </header>

        <div class="flex gap-2">
            <x-carousel>
                <img slot="image" src="/assets/images/exemples/brehat.jpeg" alt="img1">
                <img slot="image" src="/assets/images/exemples/7iles.jpeg" alt="img2">
                <img slot="image" src="/assets/images/exemples/PG1.jpeg" alt="img3">
                <img slot="image" src="/assets/images/exemples/PG2.jpeg" alt="img4">
                <img slot="image" src="/assets/images/exemples/PG3.jpeg" alt="img5">
                <img slot="image" src="/assets/images/exemples/PG4.jpeg" alt="img6">
                <img slot="image" src="/assets/images/exemples/PG5.webp" alt="img7">
                <img slot="image" src="/assets/images/exemples/PG6.jpg" alt="img8">
                <img slot="image" src="/assets/images/exemples/PG7.jpeg" alt="img9">
                <img slot="image" src="/assets/images/exemples/PG8.jpg" alt="img10">
                <img slot="image" src="/assets/images/exemples/PG9.jpg" alt="img11">
            </x-carousel>

        </div>
    </section>
</div>

