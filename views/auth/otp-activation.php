<?php

$this->jsFile = 'otpActivation';
$this->cssFile = 'otpActivation';

?>

<div id="otpForm" class="flex flex-col items-center justify-center gap-6 p-6 rounded-lg shadow-md w-full max-w-md mx-auto">
    <div id="qrCode" class="flex flex-col items-center">
        <img id="otpQrCode" src="<?php echo $qrCodeUri; ?>" alt="QR Code OTP" class="w-52 h-52 rounded-lg shadow-md">
        <p class="text-lg font-semibold text-gray-700 mt-2">Scannez ce QR Code</p>
    </div>

    <form id="otpActivation" class="w-full flex flex-col gap-4">
        <label for="otp_input" class="text-sm font-medium text-gray-600">Entrez votre code d'authentification :</label>
        <input
            name="otp"
            id="otp_input"
            type="text"
            placeholder="6 chiffres"
            maxlength="6"
            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
        <button
            type="submit"
            class="w-full bg-blue-600 text-white py-2 rounded-md font-semibold hover:bg-blue-700 transition duration-200"
        >Vérifier</button>
    </form>
    <p id="otpCodeError" class="text-sm font-medium text-red-500 hidden"></p>
</div>

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
