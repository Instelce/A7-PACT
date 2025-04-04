<?php
$this->jsFile = 'otpActivation';
$this->cssFile = 'otpActivation';
?>

<div id="otpActivationContainer" class="min-h-screen flex items-center justify-center px-4">
    <div class="flex flex-col items-center justify-center gap-6 p-6 rounded-lg shadow-md w-full max-w-md">
        <div id="qrCode" class="flex flex-col items-center">
            <img id="otpQrCode" src="<?php echo $qrCodeUri; ?>" alt="QR Code OTP" class="w-52 h-52 rounded-lg shadow-md">
            <p class="text-lg font-semibold text-gray-700 mt-2 text-center">Scannez ce QR Code avec Google Authenticator</p>
        </div>

        <form id="otpActivationForm" class="w-full flex flex-col gap-4">
            <p class="text-sm text-gray-600 text-center">
                Une fois scanné, entrez le code à 6 chiffres généré par Google Authenticator.
            </p>
            <label for="otp_input" class="text-sm font-medium text-gray-600">Code à 6 chiffres :</label>

            <div id="otpInputFields" class="flex justify-between gap-4">
                <input type="text" maxlength="1" class="otp-input text-center" id="otp-1" autocomplete="one-time-code" />
                <input type="text" maxlength="1" class="otp-input text-center" id="otp-2" autocomplete="one-time-code" />
                <input type="text" maxlength="1" class="otp-input text-center" id="otp-3" autocomplete="one-time-code" />
                <input type="text" maxlength="1" class="otp-input text-center" id="otp-4" autocomplete="one-time-code" />
                <input type="text" maxlength="1" class="otp-input text-center" id="otp-5" autocomplete="one-time-code" />
                <input type="text" maxlength="1" class="otp-input text-center" id="otp-6" autocomplete="one-time-code" />
            </div>

            <button type="submit" class="button w-full">Activer</button>
        </form>

        <p id="otpCodeError" class="text-sm font-medium text-red-500 hidden text-center"></p>
    </div>
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
