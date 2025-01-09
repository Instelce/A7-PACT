<?php
/** @var $this \app\core\View */

use app\core\form\Form;

$this->title = 'ResetPassword';
$this->jsFile = 'resetPassword';

?>
<div class="form-page form-page-little">
    <h1 class="heading-1">Réinitialisation du mot de passe</h1>
    <form method="post" id="reset-password" class="form-inputs">
        <input type="hidden" name="token" value="<?php echo $hash; ?>">
        <div class="password-check">
            <x-input id="password">
                <label slot="label">Nouveau mot de passe</label>
                <input slot="input" name="password" type="password" placeholder="************" required>
            </x-input>
            <div class="hidden password-requirements">
                <p>Le mot de passe doit contenir au moins :</p>
                <ul>
                    <li class="invalid letter">
                        <span class="icon"></span> Une minuscule
                    </li>
                    <li class="invalid capital">
                        <span class="icon"></span> Une majuscule
                    </li>
                    <li class="invalid number">
                        <span class="icon"></span> Un nombre
                    </li>
                    <li class="invalid special">
                        <span class="icon"></span> Un caractère spécial
                    </li>
                    <li class="invalid length">
                        <span class="icon"></span> Au minimum 12 caractères
                    </li>

                </ul>
            </div>
        </div>
        <x-input id="passwordConfirm">
            <label slot="label">Confirmez le nouveau mot de passe</label>
            <input slot="input" name="password" type="password" placeholder="************" required>
        </x-input>

        <button id ="passwordModify" type="submit" class="button w-full gray">Confirmer</button>
    </form>
</div>