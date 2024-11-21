<?php

use app\core\Application;

$this->title = "Profile";
$this->cssFile = "profile";

$user = Application::$app->user;
?>

<div class="profile-up">
  <div class="profile-up">
    <img class="profile-page-pic" src="<?php echo Application::$app->user->avatar_url ?>">
    <i data-lucide="pen_line"></i>
    <div>
      <h1 class="heading-2 gap-1">Profil de <span class="underline"><?php echo $user->mail ?></span></h1>
      <h3 class="heading-3 gap-1"> <i data-lucide="message-circle"></i> <span class="pb-1"> Avis</h3>
      <h3 class="flex heading-3 align-center gap-1"> <i data-lucide="heart"></i> <span class="pt-1"> Likes</h3>

      
      <h3 class="heading-3"><i data-lucide="badge-check"></i> <span class="pb-1"><?php if(Application::$app->user->isPrivateProfessional()){
        echo " Professionnel Privé";
        } elseif(Application::$app->user->isPublicProfessional()){
          echo " Professionnel Publique";
        } else{
          echo " Membre";
        }
        ?>
        </h3>

    </div>
    

  </div>
</div>

<div class="flex flex-col gap-4">
    <x-tabs>
      <x-tab class="profile-up-bandeau profile-up-bandeau-l" role="heading" slot="tab">Offre</x-tab>
      <x-tab-panel role="region" slot="panel">
          <p>QUOICOU</p>
          <div>
            
          </div>
      </x-tab-panel>

      <x-tab class="profile-bandeau profile-bandeau-r" role="heading" slot="tab">Réponse</x-tab>
      <x-tab-panel role="region" slot="panel">
          <p>BEUW</p>
      </x-tab-panel>
    </x-tabs>
</div>


<!--

<img src="https://content.imageresizer.com/images/memes/One-Piece-Enel-Shocked-meme-9.jpg">

!-->