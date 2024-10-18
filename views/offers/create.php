<?php
/** @var $model \app\models\Offer */
/** @var $offerTags \app\models\OfferTag[] */
/** @var $this \app\core\View */

$this->title = "Création d'une offre";
$this->jsFile = "offerCreate";

?>

<div class="w-full flex flex-col">
    <div class="w-full flex items-center justify-center py-16">
        <h1 class="heading-1">Créer une offre</h1>
    </div>

    <?php $form = \app\core\form\Form::begin('', '', 'flex flex-col gap-8') ?>

    <!-- ------------------------------------------------------------------- -->
    <!-- Type of the offer (type: 'standard' | 'premium')                    -->
    <!-- ------------------------------------------------------------------- -->

    <section>
        <h2 class="section-header">Type <span class="text-gray-3 ml-1">- Uniquement visible par vous</span></h2>

        <!-- Choice for a PRIVATE professional -->
        <div class="flex flex-col gap-4">

          <!-- Standard offer -->
          <div class="flex justify-between">
              <div class="flex gap-2">
                  <input type="radio" id="type-standard" name="type" value="standard" checked>
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
        </div>

        <!-- TODO: Do PUBLIC professional choices -->
    </section>


    <!-- ------------------------------------------------------------------- -->
    <!-- Option of the offer (option: 'no' | 'en-relief' | 'a-la-une')       -->
    <!-- ------------------------------------------------------------------- -->

    <section>
      <h2 class="section-header">Option <span class="text-gray-3 ml-1">- Uniquement visible par vous</span></h2>

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
              <input type="radio" id="type-in-relief" name="option" value="en-relief">
              <label for="type-in-relief" class="flex flex-col gap-1">
                Option “En Relief”
                <small class="helper">Met l’offre en exergue lors de son affichage dans les listes</small>
              </label>
            </div>

            <span class="price-bubble">+ 2,98 €</span>
        </div>

        <!-- A la une option -->
        <div class="flex justify-between items-center">
            <div class="flex gap-2 items-center">
              <input type="radio" id="type-a-la-une" name="option" value="a-la-une">
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
            <p slot="label">Date d'action</p>
            <!--<i slot="icon-right" data-lucide="calendar-days"></i>-->
            <input slot="input" type="date" placeholder="Placeholder">
          </x-input>
          <x-input>
            <p slot="label">Date d'arrêt</p>
            <!--<i slot="icon-right" data-lucide="calendar-days"></i>-->
            <input slot="input" type="date" placeholder="Placeholder">
          </x-input>
        </div>
      </div>
    </section>


    <!-- ------------------------------------------------------------------- -->
    <!-- Information of the option                                           -->
    <!-- ------------------------------------------------------------------- -->

    <section>
        <h2 class="section-header">Informations</h2>

        <div class="flex flex-col gap-2">
            <?php echo $form->field($model, 'title') ?>
            <?php echo $form->textarea($model, 'description') ?>
            <?php echo $form->textarea($model, 'summary') ?>

            <!-- Category of the offer -->
            <x-select id="category" name="category">
              <label slot="label">Catégorie</label>
              <span slot="trigger">Choisir une categorie</span>
              <div slot="options">
                <div data-value="visit">Visite</div>
                <div data-value="activity">Activité</div>
                <div data-value="restaurant">Restaurant</div>
                <div data-value="show">Spectacle</div>
                <div data-value="attraction-park">Parc d'attraction</div>
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
    <!-- Schedules                                                           -->
    <!-- ------------------------------------------------------------------- -->

    <section>
        <h2 class="section-header">Horaires</h2>

        <div class="flex flex-col gap-4">
            <!-- Toggle schedule -->
            <div class="flex items-center gap-4">
                <div class="flex">
                    <input class="switch" type="checkbox" id="enable-schedule" />
                    <label class="switch" for="enable-schedule"></label>
                </div>

                <label for="enable-schedule">Activer les horaires</label>
            </div>

            <!-- Schedule table -->
            <table id="schedule-table" class="table center hidden">
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
    <!-- Localisation                                                        -->
    <!-- ------------------------------------------------------------------- -->

    <section>
        <h2 class="section-header">Localisation</h2>

        <div class="flex flex-col gap-2">
            <x-input>
                <p slot="label">Ville</p>
                <input slot="input" type="text" placeholder="" required>
            </x-input>
            <x-input>
                <p slot="label">Rue / Lieu-dit</p>
                <input slot="input" type="text" placeholder="" required>
            </x-input>
        </div>
    </section>


    <!-- ------------------------------------------------------------------- -->
    <!-- Price                                                               -->
    <!-- ------------------------------------------------------------------- -->

    <section>
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

        <!-- Schedule table -->
        <table id="price-table" class="table center mt-4 hidden">
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
                        <input id="price-name" name="prices[]" type="text" placeholder="Nom" class="table-input">
                    </td>
                    <td>
                        <input id="price-value" name="prices[]" type="number" placeholder="Prix" class="table-input">
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
    </section>

    <!-- ------------------------------------------------------------------- -->
    <!-- Photos                                                              -->
    <!-- ------------------------------------------------------------------- -->

    <section>
        <h2 class="section-header">Photos ou illustrations</h2>

        <div class="flex flex-col gap-2">

        </div>
    </section>

    <?php \app\core\form\Form::end(); ?>
</div>