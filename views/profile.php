<?php
/** @var $user \app\model\account\UserAccount
 * @var $this \app\core\View 
 */

use app\core\Application;
use app\core\Utils;
use app\models\offer\Offer;


$this->title = "Profile";
$this->cssFile = "profile";
$this->jsFile = "profile";
?>

<div class="profile-up">
  <div class="profile-up">
    <img class="profile-page-pic" src="<?php echo $user->avatar_url ?>">
    <i data-lucide="pen_line"></i>
    <div>
      <h1 class="heading-2 gap-1">Profil de <span class="underline"><?php echo $user->specific()->denomination ?></span></h1>
      <h3 class="heading-3 gap-1"> <i data-lucide="message-circle"></i> <span class="pb-1"> Avis</h3>
      <h3 class="flex heading-3 align-center gap-1"> <i data-lucide="heart"></i> <span class="pt-1"> Likes</h3>
      <h3 class="heading-3"><i data-lucide="badge-check"></i> <span class="pb-1">
        <?php if ($user->isPublicProfessional()) {?>
            Professionnel publique
        <?php } elseif ($user->isPrivateProfessional()) {?>
            Professionnel Privé
        <?php } else { ?>
            Membre
        <?php }?>
   </span></h3>
    </div>
  </div>
</div>

<div class="flex flex-col gap-4">
  <x-tabs>
    <x-tab class="profile-up-bandeau profile-up-bandeau-l" id="offres" role="heading" slot="tab">Offre</x-tab>
    <x-tab-panel role="region" slot="panel">
        <!-- All offers generated in js file -->
        <div id="offers-container">
            <div id="offers-loader"></div>
        </div>
    </x-tab-panel>

    <x-tab class="profile-bandeau profile-up-bandeau-r" id="reponses" role="heading" slot="tab">Réponse</x-tab>
    <x-tab-panel role="region" slot="panel">

    </x-tab-panel>
  </x-tabs>
</div>


<!--

<img src="https://content.imageresizer.com/images/memes/One-Piece-Enel-Shocked-meme-9.jpg">

!-->