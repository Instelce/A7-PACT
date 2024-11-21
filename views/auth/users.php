<?php

/** @var $this \app\core\View */
/** @var $users \app\models\account\UserAccount[] */

$this->title = "Liste des utilisateurs";
?>

<p>Quand vous cliquez sur l'email d'un utilisateur il est copié.</p>

<div class="flex flex-col gap-4 mt-6">
    <?php foreach ($users as $user) { ?>
        <div class="user flex gap-2 cursor-pointer">
            <p class="email"><?php echo $user->mail ?></p>

            <?php if ($user->isMember()) { ?>
                <p>Member</p>
            <?php } ?>
        </div>
    <?php } ?>
</div>

<script>
    let users = document.querySelectorAll('.user');
    for (const user of users) {
        user.addEventListener('click', () => {
            // Add to clipboard
            let email = user.querySelector('.email').textContent
            navigator.clipboard.writeText(email);
            user.querySelector('.email').textContent = "Copié !";
            user.style.color = "green";
            setTimeout(() => {
                user.querySelector('.email').textContent = email;
                user.style.color = "black";
            }, 1000);
        });
    }
</script>