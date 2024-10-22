<?php
/** @var $model \app\models\offer\Offer */
/** @var $offerTags \app\models\offer\OfferTag[] */
/** @var $this \app\core\View */

use app\core\form\Form;

$this->title = "Détails d'une offre";
$this->jsFile = "detailedOffer";

echo "<pre>";
var_dump($pk);
echo "</pre>";

?>

<!---- Publication date ---->

<div class="publication">
    <p>Paru le </p>
    <p>12/04/2023</p>
</div>

<!---- Carousel ---->
<div class="paddingOfferDetailed">
    <x-carousel>
        <img slot="image" src="/assets/images/exemples/brehat.jpeg" alt="img1">
        <img slot="image" src="/assets/images/exemples/7iles.jpeg" alt="img2">
        <img slot="image" src="/assets/images/exemples/PG1.jpeg" alt="img3">
        <img slot="image" src="/assets/images/exemples/PG2.jpeg" alt="img4">
        <img slot="image" src="/assets/images/exemples/PG3.jpeg" alt="img5">
        <img slot="image" src="/assets/images/exemples/PG4.jpeg" alt="img6">
        <img slot="image" src="/assets/images/exemples/PG5.webp" alt="img7">
        <img slot="image" src="/assets/images/exemples/PG6.jpg" alt="img8">
        <img slot="image" src="/assets/images/exemples/PG7.jpeg" alt="img9">
        <img slot="image" src="/assets/images/exemples/PG8.jpg" alt="img10">
        <img slot="image" src="/assets/images/exemples/PG9.jpg" alt="img11">
    </x-carousel>


    <!---- Infos ---->

    <h2 class="heading-2">Balade familiale à vélo “Qui m’aime me suive”</h2>

    <div class="inlineOffer">
        <p>Par Trégor Bicyclette</p>
        <div class="inlineOfferGap">

            <p>Activité</p>
            <div class="inlineOffer">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin">
                    <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/>
                    <circle cx="12" cy="10" r="3"/>
                </svg>
                <p>Bréhat</p>
            </div>

            <div class="inlineOffer">
                <p>Durée</p>
                <p>6H</p>
            </div>

            <div class="inlineOffer">
                <p>A partir de </p>
                <p>12 ans</p>
            </div>
        </div>
    </div>

    <div>
        <h2 class="heading-2">Résumé :</h2>
        <br>
        <p>Montrer que l'on peut réaliser localement de belles balades à vélo, en empruntant de petites routes tranquilles et sans trop de montées
        </p>
    </div>

    <div class="columnOffer">

        <div class="inlineOffer">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin">
                <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/>
                <circle cx="12" cy="10" r="3"/>
            </svg>
            <p><a href="https://www.google.fr/maps/place/3+All.+des+Soupirs,+22300+Lannion/@48.7283667,-3.461672,17z/data=!3m1!4b1!4m6!3m5!1s0x48122be9c2f84ea7:0x891eae3d19746eed!8m2!3d48.7283632!4d-3.4590971!16s%2Fg%2F11cpnp2qhh?entry=ttu&g_ep=EgoyMDI0MTAxNi4wIKXMDSoASAFQAw%3D%3D" target="_blank">
                3 Allée des Soupirs, 22300 Lannion</a></p>
        </div>

        <div class="inlineOffer">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-coins"><circle cx="8" cy="8" r="6"/><path d="M18.09 10.37A6 6 0 1 1 10.34 18"/><path d="M7 6h1v4"/><path d="m16.71 13.88.7.71-2.82 2.82"/></svg>
            <p>Gratuit</p>
        </div>

        <div class="inlineOffer">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <p>Ouvert</p>
        </div>

        <div class="inlineOffer">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-globe"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/></svg>
            <p><a href="https://www.tregorbicyclette.fr/" target="_blank">https://www.tregorbicyclette.fr/</a></p>
        </div>

        <div class="inlineOffer">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
            <p>01 23 45 67 89</p>
        </div>

        <div class="inlineOffer">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-languages"><path d="m5 8 6 6"/><path d="m4 14 6-6 2-3"/><path d="M2 5h12"/><path d="M7 2h1"/><path d="m22 22-5-10-5 10"/><path d="M14 18h6"/></svg>
            <p>Français, Anglais</p>
        </div>
    </div>

    <div>
        <h2 class="heading-2">Description :</h2>
        <br>
        <p>Les sorties sont volontairement limitées entre 15 km et 20 km pour permettre à un large public familial de se joindre à nous. A partir de 6 ou 7 ans, un enfant à l'aise sur son vélo, peut en général parcourir une telle distance sans problème : le rythme est suffisamment lent (adapté aux plus faibles), avec des pauses, et le fait d'être en groupe est en général un bon stimulant pour les enfants ... et les plus grands ! Les plus jeunes peuvent aussi participer en charrette, sur un siège vélo ou bien avec une barre de traction.
        </p>
    </div>

    <div class="inlineOffer">
        <h2 class="heading-2">Tags : </h2>
        <p class="heading-3" >Plein air, Sport, Famille</p>
    </div>


    <div class="acordeonSize">
        <x-acordeon text="Grille tarifaire">
            <div slot="content">
                <p>Adhérent enfant : 0 € <br>
                    Adhérent adulte : 2 € <br>
                    Non adhérent enfant : 10 € <br>
                    Non adhérent adulte : 15 €
                </p>
            </div>
        </x-acordeon>

        <x-acordeon text="Prestations incluses">
            <div slot="content">
                <p>Encadrant <br>
                    Kit de crevaison <br>
                    Déjeuner et sandwich
                </p>
            </div>
        </x-acordeon>

        <x-acordeon text="Prestations non incluses">
            <div slot="content">
                <p>Bicyclette <br>
                    Crème solaire
                </p>
            </div>
        </x-acordeon>

        <x-acordeon text="Accessibilité">
            <div slot="content">
                <p>Le public en situation de handicap est le bienvenu, ne pas hésiter à nous appeler pour préparer la balade
                </p>
            </div>
        </x-acordeon>
    </div>



</div>

