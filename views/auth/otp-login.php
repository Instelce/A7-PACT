<?php

$this->jsFile = 'otpLogin';
$this->cssFile = 'otpLogin';

?>

<div class="otp-wrapper">
    <div class="otp-container">
        <h1>Vérification en deux étapes</h1>
        <p>Pour vous connecter, entrez le code à 6 chiffres de Google Authenticator.</p>

        <form id="otpLoginForm" method="POST">
            <div class="otp-inputs">
                <input type="text" inputmode="numeric" maxlength="1" class="otp-box" />
                <input type="text" inputmode="numeric" maxlength="1" class="otp-box" />
                <input type="text" inputmode="numeric" maxlength="1" class="otp-box" />
                <input type="text" inputmode="numeric" maxlength="1" class="otp-box" />
                <input type="text" inputmode="numeric" maxlength="1" class="otp-box" />
                <input type="text" inputmode="numeric" maxlength="1" class="otp-box" />
            </div>
            <input type="hidden" name="otpLogin" id="otpHidden" />

            <button type="submit" class="button w-full">Vérifier le code</button>
        </form>
        <p id="otpError" class="text-sm font-medium text-red-500 hidden"></p>
    </div>
</div>



