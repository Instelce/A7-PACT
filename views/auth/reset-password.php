<div class="form-page form-page-little">
    <h1 class="heading-1">Réinitialisation du mot de passe</h1>
    <form method="post" id="reset-password" class="form-inputs">
        <input type="hidden" name="token" value="<?php echo $hash; ?>">
        <x-input id="new-password">
            <label slot="label">Nouveau mot de passe</label>
            <input slot="input" name="password" type="password" placeholder="" required>
        </x-input>
        <button id ="passwordModify" type="submit" class="button w-full gray">Confirmer</button>
    </form>
</div>