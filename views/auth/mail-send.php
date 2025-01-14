<?php
$this->title = 'Mail Envoye';
/** @var $mail string*/
?>

<div class="form-page form-page-little">
    <div class="h-auth">
        <h1 class="heading-1">Email envoyé</h1>
    </div>
    <div class="flex flex-col gap-2">
        <p class="font-normal">Un email de réinitialisation de mot de passe a été envoyé l'adresse <strong><?php echo htmlspecialchars($mail); ?></strong>. Veuillez vérifier votre boîte de réception.</p>
        <form method="post" class="flex flex-row gap-2">
            <p class="font-normal">Si vous n'avez pas reçu l'email, vous pouvez</p>
            <button type="submit" class="link">renvoyer l'email</button>
        </form>
        </div>
    </div>
</div>