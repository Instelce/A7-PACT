<?php
/** @var $pseudo string */
$url = $_ENV['DOMAIN'] . "/comptes/reset-password?token=$hash";
?>

<h1>Modifier votre mot de passe PACT</h1>

<p>Cliquez sur le bouton ci-dessous pour modifier votre mot de passe</p>
<a href="<?php echo $url ?>">Modifier mon mot de passe</a>


<p>L'équipe PACT</p>