<?php
/** @var $model \app\models\offer\Offer */
/** @var $offerTags \app\models\offer\OfferTag[] */

/** @var $this \app\core\View */

use app\core\Application;
use app\core\form\Form;

$this->title = "Création d'une offre";
$this->jsFile = "offerCreate";

$form = new Form();

?>

<!-- Title -->
<div class="w-full flex items-center justify-center py-16">
    <h1 class="heading-1">Créer une offre</h1>
</div>


<div class="grid grid-cols-5 gap-8">
    <div class="w-full flex flex-col col-span-3">

        <form action="" method="post" id="create-offer" class="flex flex-col gap-12"
              enctype="multipart/form-data">

            <!-- ------------------------------------------------------------------- -->
            <!-- Type of the offer (type: 'standard' | 'premium')                    -->
            <!-- ------------------------------------------------------------------- -->

            <section>
                <h2 class="section-header">Type <span class="text-gray-3 ml-1">- Uniquement visible par vous</span>
                </h2>

                <!-- Choice for a PRIVATE professional -->
                <div class="flex flex-col gap-4">

                    <?php if (Application::$app->user->isPrivateProfessional()) { ?>

                        <!-- Standard offer -->
                        <div class="flex justify-between">
                            <div class="flex gap-2">
                                <input type="radio" id="type-standard" name="type" value="standard"
                                       checked>
                                <label for="type-standard" class="flex flex-col gap-1">
                                    Offre standard
                                    <small class="helper">Permet de prendre une option</small>
                                </label>
                            </div>

                            <span class="price-bubble">4,98 €</span>
                        </div>

                        <!-- Premium offer -->
                        <div class="flex justify-between items-center">
                            <div class="flex gap-2 items-center">
                                <input type="radio" id="type-premium" name="type" value="premium">
                                <label for="type-premium" class="flex flex-col gap-1">
                                    Offre premium
                                    <small class="helper">Avantage de l’offre standard <br>
                                        Permet de blacklister 3 avis néfaste à votre offre
                                        sur 12 mois glissants</small>
                                </label>
                            </div>

                            <span class="price-bubble">7,98 €</span>
                        </div>

                    <?php } else { ?>

                        <!-- Free offer -->
                        <div class="flex justify-between items-center">
                            <div class="flex gap-2 items-center">
                                <label for="type-free" class="flex flex-col gap-1">
                                    Offre gratuite
                                </label>
                            </div>

                            <span class="price-bubble">0 €</span>
                        </div>

                    <?php } ?>

                </div>

            </section>


            <!-- ------------------------------------------------------------------- -->
            <!-- Option of the offer (option: 'no' | 'en-relief' | 'a-la-une')       -->
            <!-- ------------------------------------------------------------------- -->

            <?php if (Application::$app->user->isPrivateProfessional()) { ?>
                <section>
                    <h2 class="section-header">Option <span class="text-gray-3 ml-1">- Uniquement visible par vous</span>
                    </h2>

                    <div class="flex flex-col gap-4">

                        <!-- No option -->
                        <div class="flex gap-2 items-center">
                            <input type="radio" id="type-no" name="option" value="no" checked>
                            <label for="type-no" class="flex flex-col gap-1">
                                Aucune option
                            </label>
                        </div>

                        <!-- En relief option -->
                        <div class="flex justify-between items-center">
                            <div class="flex gap-2 items-center">
                                <input type="radio" id="type-in-relief" name="option"
                                       value="en_relief">
                                <label for="type-in-relief" class="flex flex-col gap-1">
                                    Option “En Relief”
                                    <small class="helper">Met l’offre en exergue lors de son
                                        affichage dans les listes</small>
                                </label>
                            </div>

                            <span class="price-bubble">+ 2,98 €</span>
                        </div>

                        <!-- A la une option -->
                        <div class="flex justify-between items-center">
                            <div class="flex gap-2 items-center">
                                <input type="radio" id="type-a-la-une" name="option"
                                       value="a_la_une">
                                <label for="type-a-la-une" class="flex flex-col gap-1">
                                    Option “A la Une”
                                    <small class="helper">Avantage de “En relief” <br>
                                        Met l’offre en avant sur la page d’accueil</small>
                                </label>
                            </div>
                            <span class="price-bubble">+ 4,98 €</span>
                        </div>

                        <!-- Start end end DATE of the option -->
                        <div id="option-dates" class="flex gap-4 mt-2 w-full hidden">
                            <x-input>
                                <p slot="label">Date de lancement</p>
                                <!--<i slot="icon-right" data-lucide="calendar-days"></i>-->
                                <input slot="input" type="date" name="option-launch-date">
                            </x-input>
                            <x-input>
                                <p slot="label">Nombre de semaine</p>
                                <!--<i slot="icon-right" data-lucide="calendar-days"></i>-->
                                <input slot="input" type="number" name="option-duration" max="4"
                                       min="1" value="1">
                            </x-input>
                        </div>
                    </div>
                </section>
            <?php } ?>


            <!-- ------------------------------------------------------------------- -->
            <!-- Information of the option                                           -->
            <!-- ------------------------------------------------------------------- -->

            <section>
                <h2 class="section-header">Informations</h2>

                <div class="flex flex-col gap-2">
                    <?php echo $form->field($model, 'title') ?>
                    <?php echo $form->textarea($model, 'summary', 3) ?>
                    <?php echo $form->textarea($model, 'description', 6) ?>

                    <!-- Category of the offer -->
                    <x-select id="category" name="category" required>
                        <label slot="label">Catégorie</label>
                        <span slot="trigger">Choisir une categorie</span>
                        <div slot="options">
                            <div data-value="visit">Visite</div>
                            <div data-value="activity">Activité</div>
                            <div data-value="restaurant">Restaurant</div>
                            <div data-value="show">Spectacle</div>
                            <div data-value="attraction-parc">Parc d'attraction</div>
                        </div>
                    </x-select>

                    <!-- Tags of the category -->
                    <!-- Generated in the offerCreate.js -->
                    <div id="tags" class="grid grid-cols-2 my-2"></div>

                    <!-- Site web of the offer -->
                    <?php echo $form->field($model, 'website') ?>
                    <?php echo $form->field($model, 'phone_number') ?>
                </div>
            </section>


            <!-- ------------------------------------------------------------------- -->
            <!-- Complementary data                                                       -->
            <!-- ------------------------------------------------------------------- -->

            <section id="complementary-section">

                <h2 class="section-header">Informations complémentaires </h2>

                <p id="category-no-selected">Veuillez selectionnez une catégorie.</p>

                <!-- Visit -->

                <div class="complementary-section flex flex-col gap-4 hidden" data-category="visit">

                    <x-input>
                        <p slot="label">Durée de la visite (h)</p>
                        <input slot="input" type="text" name="visit-duration" placeholder="2h15"
                               required>
                    </x-input>

                    <x-input>
                        <p slot="label">Langues disponibles pour la visite</p>
                        <input slot="input" type="text" name="visit-languages"
                               placeholder="anglais,français..." required>
                        <p slot="helper">Séparez les langues par une ,</p>
                    </x-input>

                    <div class="flex gap-4 items-center">
                        <div class="flex items-center">
                            <input class="switch" type="checkbox" id="switch-guide"
                                   name="visit-guide"/>
                            <label class="switch" for="switch-guide"></label>
                        </div>
                        <label for="switch-guide">Avec guide de visite</label>
                    </div>

                </div>

                <!-- Show -->
                <div class="complementary-section flex flex-col gap-4 hidden" data-category="show">
                    <x-input>
                        <p slot="label">Durée du spectacle (h)</p>
                        <input slot="input" type="text" name="show-duration" placeholder="1h30"
                               required>
                    </x-input>

                    <x-input>
                        <p slot="label">Capacité d'accueil spectacle</p>
                        <input slot="input" type="number" name="show-capacity" placeholder="100"
                               required>
                    </x-input>

                </div>

                <!-- Activity -->
                <div class="complementary-section flex flex-col gap-4 hidden"
                     data-category="activity">
                    <x-input>
                        <p slot="label">Durée de l'activité (h)</p>
                        <input slot="input" type="text" name="activity-duration" placeholder="1h30"
                               required>
                    </x-input>

                    <x-input>
                        <p slot="label">Age minimum pour l'activité</p>
                        <input slot="input" type="number" name="activity-age" placeholder="3"
                               required>
                    </x-input>
                </div>

                <!-- Restaurant -->
                <div class="complementary-section flex flex-col gap-4 hidden"
                     data-category="restaurant">
                    <div class="flex flex-col gap-2">
                        <label for="restaurant-image">Image de la carte</label>
                        <input id="restaurant-image" type="file" accept="image/png, image/jpg"
                               name="restaurant-image" required>
                    </div>


                    <!-- Range price for the RESTAURANT -->
                    <div class="flex gap-4">

                        <x-select id="price-low" name="price-range" value="1" class="w-full"
                                  required>
                            <label slot="label">Gamme de prix</label>
                            <span slot="trigger">€</span>
                            <div slot="options">
                                <div data-value="1">€ - menu à moins de 25€</div>
                                <div data-value="2">€€ - entre 25 et 40€</div>
                                <div data-value="3">€€€ - au-delà de 40€</div>
                            </div>
                        </x-select>

                    </div>

                    <!--                Meal-->
                    <!--                <table class="table center hidden">-->
                    <!--                    <thead>-->
                    <!--                    <tr>-->
                    <!--                        <th class="table-head" colspan="2">Repas</th>-->
                    <!--                    </tr>-->
                    <!--                    <tr>-->
                    <!--                        <th>Nom</th>-->
                    <!--                        <th>Prix</th>-->
                    <!--                    </tr>-->
                    <!--                    </thead>-->
                    <!---->
                    <!--                    <tbody id="meal">-->
                    <!--                    <tr>-->
                    <!--                        <td>-->
                    <!--                            <input id="meal-name" name="prices[]" type="text" placeholder="Nom" class="table-input">-->
                    <!--                        </td>-->
                    <!--                        <td>-->
                    <!--                            <input id="meal-price" name="prices[]" type="number" placeholder="Prix" class="table-input">-->
                    <!--                        </td>-->
                    <!--                    </tr>-->
                    <!--                    </tbody>-->
                    <!---->
                    <!--                    <tfoot>-->
                    <!--                    <tr>-->
                    <!--                        <td colspan="2">-->
                    <!--                            <button id="add-price-row" class="table-button">-->
                    <!--                                Ajouter un tarif-->
                    <!--                                <i data-lucide="plus"></i>-->
                    <!--                            </button>-->
                    <!--                        </td>-->
                    <!--                    </tr>-->
                    <!--                    </tfoot>-->
                    <!--                </table>-->
                </div>

                <!-- Attraction parc-->
                <div class="complementary-section flex flex-col gap-4 hidden"
                     data-category="attraction-parc">
                    <div class="flex flex-col gap-2">
                        <label for="attraction-parc-map">Plan du parc</label>
                        <input id="attraction-parc-map" type="file" accept="image/png, image/jpg"
                               name="attraction-parc-map" required>
                    </div>

                    <x-input>
                        <p slot="label">Age minimum requis</p>
                        <input slot="input" type="number" name="attraction-min-age" required>
                    </x-input>
                </div>


                <!-- Period for visit and show -->
                <div id="period-section" class="flex flex-col gap-4 mt-4 hidden">

                    <div class="flex gap-4 items-center">
                        <div class="flex items-center">
                            <input class="switch" type="checkbox" id="switch-period"
                                   name="visit-guide"/>
                            <label class="switch" for="switch-period"></label>
                        </div>
                        <label for="switch-period" id="switch-period-label">A une période</label>
                    </div>

                    <div id="period-fields" class="flex gap-4 hidden"></div>
                </div>

            </section>


            <!-- ------------------------------------------------------------------- -->
            <!-- Localisation                                                        -->
            <!-- ------------------------------------------------------------------- -->

            <section>
                <h2 class="section-header">Localisation</h2>

                <div class="flex flex-col gap-4">
                    <x-input>
                        <p slot="label">Adresse complète</p>
                        <input slot="input" id="address-field" type="text" placeholder="">
                        <p slot="helper">Champ avec suggestions qui modifie les champs suivants</p>
                        <div slot="list" id="address-autocomplete" data-no-filter></div>
                    </x-input>

                    <div class="flex flex-col gap-2">
                        <div class="flex gap-4">
                            <x-input class="w-[200px]">
                                <p slot="label">Numéro de rue</p>
                                <input slot="input" id="address-number" type="number"
                                       name="address-number" placeholder="2">
                            </x-input>
                            <x-input>
                                <p slot="label">Nom de la rue</p>
                                <input slot="input" id="address-street" type="text"
                                       name="address-street" placeholder="Rue Edouard Branly"
                                       required>
                            </x-input>
                        </div>
                        <div class="flex gap-4">
                            <x-input class="w-[200px]">
                                <p slot="label">Code postal</p>
                                <input slot="input" id="address-postal-code" type="text"
                                       name="address-postal-code" placeholder="22300" required>
                            </x-input>
                            <x-input>
                                <p slot="label">Ville</p>
                                <input slot="input" id="address-city" type="text"
                                       name="address-city" placeholder="Lannion" required>
                            </x-input>
                        </div>

                        <!-- Longitude and latitude inputs -->
                        <input id="address-latitude" type="hidden" name="address-latitude">
                        <input id="address-longitude" type="hidden" name="address-longitude">
                    </div>
                </div>

            </section>


            <!-- ------------------------------------------------------------------- -->
            <!-- Schedules                                                           -->
            <!-- ------------------------------------------------------------------- -->

            <section id="schedules-section" class="hidden">
                <h2 class="section-header">Horaires</h2>

                <div class="flex flex-col gap-4">

                    <!-- Schedule table -->
                    <table id="schedule-table" class="table center">
                        <thead>
                        <tr>
                            <th class="table-head" colspan="3">Grille des horaires</th>
                        </tr>
                        <tr>
                            <th>Jour</th>
                            <th>Ouverture</th>
                            <th>Fermeture</th>
                        </tr>
                        </thead>

                        <!-- Days of the week are generated in the offerCreate.js -->
                        <tbody id="schedules-rows"></tbody>
                    </table>
                </div>
            </section>

            <!-- ------------------------------------------------------------------- -->
            <!-- Price                                                               -->
            <!-- ------------------------------------------------------------------- -->

            <section class="flex flex-col hidden" id="price-section">
                <h2 class="section-header">Vos prix</h2>

                <div class="flex flex-col gap-2">

                    <!-- Free offer -->
                    <div class="flex gap-2 items-center">
                        <input type="radio" id="price-free" name="price" value="free" checked>
                        <label for="price-free" class="flex flex-col gap-1">Offre gratuite</label>
                    </div>

                    <!-- Paying offer -->
                    <div class="flex gap-2 items-center">
                        <input type="radio" id="price-paying" name="price" value="paying">
                        <label for="price-paying" class="flex flex-col gap-1">Offre payante</label>
                    </div>

                </div>

                <div id="price-fields" class="flex flex-col gap-4 hidden">

                    <!-- Minimum price for offers that NOT RESTAURANT -->
                    <div class="flex gap-4 mt-4" id="offer-minimum-price">

                        <x-input>
                            <p slot="label">Prix minimum</p>
                            <input slot="input" type="number" name="restaurant-min-price" min="1">
                        </x-input>

                    </div>

                    <!-- Price table -->
                    <table class="table center hidden">
                        <thead>
                        <tr>
                            <th class="table-head" colspan="2">Grille tarifaire</th>
                        </tr>
                        <tr>
                            <th>Dénomination</th>
                            <th>Prix</th>
                        </tr>
                        </thead>

                        <tbody id="prices-rows">
                        <tr>
                            <td>
                                <input id="price-name" name="prices[]" type="text" placeholder="Nom"
                                       class="table-input">
                            </td>
                            <td>
                                <input id="price-value" name="prices[]" type="number"
                                       placeholder="Prix" class="table-input">
                            </td>
                        </tr>
                        </tbody>

                        <tfoot>
                        <tr>
                            <td colspan="2">
                                <button id="add-price-row" class="table-button">
                                    Ajouter un tarif
                                    <i data-lucide="plus"></i>
                                </button>
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </div>

            </section>


            <!-- ------------------------------------------------------------------- -->
            <!-- Photos                                                              -->
            <!-- ------------------------------------------------------------------- -->

            <section>
                <h2 class="section-header">Photos ou illustrations</h2>

                <div class="flex flex-col">

                    <!-- Name for FILES -->
                    <div id="input-name" data-name="photos"></div>

                    <!-- Uploader -->
                    <label for="photo-input" class="image-uploader">
                        <input type="file" accept="image/png, image/jpeg" name="images[]"
                               id="photo-input" multiple hidden>

                        <i data-lucide="upload"></i>
                        <p>Faire glisser des fichiers pour les uploader</p>
                        <span class="button gray">Selectionner les fichier à uploader</span>
                    </label>

                    <!-- Photos -->
                    <div id="photos" class="flex flex-col gap-2">
                        <div class="drag-line hidden"></div>
                    </div>

                </div>
            </section>

        </form>
    </div>


    <!-- ------------------------------------------------------------------- -->
    <!-- Sidebar                                                             -->
    <!-- ------------------------------------------------------------------- -->

    <aside id="sidebar" class="sticky col-span-2 h-fit mt-4 flex flex-col gap-4 top-navbar-height">
        <?php if (Application::$app->user->isPrivateProfessional()) { ?>
            <div class="flex flex-col gap-2">
                <h3 class="font-bold indent-6">Résumé</h3>

                <div class="px-6 py-4 border border-solid border-gray-1 rounded-3xl gap-1">
                    <div class="flex justify-between text-gray-4">
                        <p>Cout de l’offre</p>
                        <span id="price-without-option">4,98 €</span>
                    </div>
                    <div class="flex justify-between text-gray-4">
                        <p>+ Cout de l'option</p>
                        <span class="flex">
                            <span id="price-with-option">7,96 €</span>
                        </span>
                    </div>
                    <!--                    <div class="flex justify-between text-gray-4">-->
                    <!--                        <p>Réduction</p>-->
                    <!--                        <span>0</span>-->
                    <!--                    </div>-->
                    <!--                    <div class="flex justify-between font-bold">-->
                    <!--                        <p>Total HT</p>-->
                    <!--                        <span id="price-subtotal">7,96 €</span>-->
                    <!--                    </div>-->
                    <div class="flex justify-between text-gray-4 mt-2">
                        <p>TVA</p>
                        <span>20%</span>
                    </div>
                    <div class="flex justify-between font-bold">
                        <p>Total TTC</p>
                        <span id="price-total">7,96 €</span>
                    </div>

                </div>

                <p class="indent-6 text-gray-3">L'offre sera facturé au mois et l'option à la semaine</p>
            </div>

            <!--            <x-input rounded>-->
            <!--                <p slot="label">Code promo</p>-->
            <!--                <input slot="input" type="text" placeholder="Entrez votre code promo">-->
            <!--                <button slot="button" class="button gray sm no-border">Appliquer</button>-->
            <!--            </x-input>-->
        <?php } ?>

        <div class="flex flex-col gap-2">
            <a href="/dashboard" class="button gray">Annuler</a>
            <a href="/offres/preview" class="button gray" aria-disabled>Preview de l'offre</a>
            <?php if (Application::$app->user->isPrivateProfessional()) { ?>
                <button class="button purple" form="create-offer" type="submit">Aller au paiement
                </button>
            <?php } else { ?>
                <button class="button purple" form="create-offer" type="submit">Créer l'offre
                </button>
            <?php } ?>
        </div>
    </aside>

</div>
