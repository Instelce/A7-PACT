<?php

$this->jsFile = 'otpActivation';
$this->cssFile = 'otpActivation';

?>

<div class="flex items-center justify-between gap-4 flex-wrap">
    <img id="otpQrCode" src="<?php echo $qrCodeUri ?>" alt="">

    <form id="otpActivation" class="w-full">
        <x-input>
            <input slot="input" name="otp" id="otp_input" type="text" placeholder="Entrez votre code d'authentification" maxlength="6">
        </x-input>
        <button type="submit" class="button">Vérifier</button>
    </form>

    <div id="responseOTP" class="activation-message hidden">
        <div class="activation-message-content">
            <h1 id="activationText" class="activation-text">L'authentification à deux facteurs a été activée avec succès !</h1>
            <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>
            <dotlottie-player
                id="lottiePlayer"
                src="https://lottie.host/d7286638-7112-4427-b488-6d42aa3de109/2QdwzaWQul.lottie"
                background="transparent"
                speed="1"
                style="width: 300px; height: 300px"
                direction="1"
                playMode="forward"
                autoplay="false">
            </dotlottie-player>
        </div>
    </div>

</div>
