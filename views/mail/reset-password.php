<?php
/** @var $pseudo string */
/** @var $hash string */

use app\core\Application;

?>

<h1>Modifier votre mot de passe PACT</h1>

<p>Cliquez sur le bouton ci-dessous pour modifier votre mot de passe</p>
<a class="button" href="<?php echo Application::url("/comptes/reset-password?token=$hash") ?>">Modifier mon mot de passe</a>

<p>Ou copiez-collez ce lien dans votre navigateur:</p>
<p class="underline"><?php echo Application::url("/comptes/reset-password?token=$hash") ?></p>

<p class="sign">L'équipe PACT</p>

<footer>
    <p>Ceci est un email automatique, merci de ne pas y répondre.</p>
    <p>Si vous n'avez pas demandé de réinitialisation de mot de passe, veuillez ignorer cet email.</p>
</footer>
