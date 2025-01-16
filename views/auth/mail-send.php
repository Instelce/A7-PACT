<?php

use app\core\Application;

$this->title = 'Mail Envoye';
$mail = Application::$app->session->get('reset-password-email');

?>

<div class="form-page form-page-little">
    <div class="h-auth flex-row gap-4 items-center">
        <p class="heading-1">Email envoyé</p>
        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 64 64" fill="none">
            <circle cx="32" cy="32" r="32" fill="#E3F2FD"/>
            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#00a838" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-mail-check" x="14" y="14"><path d="M22 13V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v12c0 1.1.9 2 2 2h8"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/><path d="m16 19 2 2 4-4"/></svg>
        </svg>
    </div>
    <div class="flex flex-col gap-2">
        <div class="flex flex-row gap-2"><p class="text-lg">Un email de réinitialisation de mot de passe a été envoyé a l'adresse <p class="text-lg link"><?php echo htmlspecialchars($mail); ?>.</p></div>
        <p class="text-lg">Veuillez vérifier votre boîte de réception.</p>
        <form method="post" class="flex flex-row gap-2 items-center">
            <p class="text-lg">Si vous n'avez pas reçu l'email, vous pouvez</p>
            <button type="submit" class="link">renvoyer l'email</button>
        </form>
        </div>
    </div>
</div>