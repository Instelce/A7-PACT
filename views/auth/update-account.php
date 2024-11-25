<div class="form-page">
    <div class="flex gap-2">
        <x-tabs class="column">

            <x-tab role="heading" slot="tab">
                <i data-lucide="user"></i>
                Informations personnelles
            </x-tab>
            <x-tab-panel role="region" slot="panel">
                <?php $form = \app\core\form\Form::begin('', 'post', '', 'flex flex-col justify-center items-center') ?>
                <div class="flex flex-col w-[600px] gap-6">
                    <div class="form-inputs">
                        <div class="flex gap-4">
                            <?php echo $form->field($model, 'lastname') ?>
                            <?php echo $form->field($model, 'firstname') ?>
                        </div>

                        <?php echo $form->field($model, 'pseudo') ?>
                        <?php echo $form->field($model, 'mail') ?>
                        <?php echo $form->field($model, 'phone') ?>
                        <div class="flex gap-4">
                            <div class="w-25%">
                                <?php echo $form->field($model, 'streetNumber') ?>
                            </div>
                            <?php echo $form->field($model, 'streetName') ?>
                        </div>
                        <div class="flex gap-4">
                            <div class="w-25%">
                                <?php echo $form->field($model, 'postalCode') ?>
                            </div>
                            <?php echo $form->field($model, 'city') ?>
                        </div>
                    </div>
                    <div class="flex flex-col gap-4">
                        <div class="flex gap-4 items-center">
                            <div class="flex items-center">
                                <input class="switch" type="checkbox" id="switch-notification" name="notification" value="true" />
                                <label class="switch" for="switch-notification"></label>
                            </div>
                            <label for="switch-period" id="switch-period-label">J’autorise l’envoi de notifications concernant
                                la mise en ligne de nouvelles offres et autre</label>
                        </div>
                    </div>
                    <button type="submit" class="button w-full gray">Modifier le mot de passe</button>
                    <button type="submit" class="button w-full danger">Supprimer mon compte</button>
                    <div class="flex gap-4, mt-8">
                        <div class='w-25%'>
                            <button type="submit" class="button gray">Annuler</button>
                        </div>
                        <button type="submit" class="button w-full">Enregistrer les modifications</button>
                    </div>
                </div>
                <?php \app\core\form\Form::end() ?>
            </x-tab-panel>

            <x-tab role="heading" slot="tab">
                <i data-lucide="key"></i>
                Sécurité
            </x-tab>
            <x-tab-panel role="region" slot="panel">
                <p>Content 2</p>
            </x-tab-panel>

        </x-tabs>
    </div>
</div>


