let otpActivationForm = document.getElementById("otpActivationForm");
let otpInputs = document.querySelectorAll(".otp-input");
let otpCodeError = document.getElementById("otpCodeError");
let responseOTP = document.getElementById("responseOTP");
let lottiePlayer = document.getElementById("lottiePlayer");
let otpActivationContainer = document.getElementById("otpActivationContainer");

otpInputs.forEach((input, index) => {
    input.addEventListener("input", function () {
        if (input.value.length === 1 && index < otpInputs.length - 1) {
            otpInputs[index + 1].focus();
        }
        if (input.value.length === 0 && index > 0) {
            otpInputs[index - 1].focus();
        }
    });

    input.addEventListener("keydown", function (e) {
        if (e.key === "Backspace" && input.value.length === 0 && index > 0) {
            otpInputs[index - 1].focus();
        }
    });
});

otpActivationForm.addEventListener("submit", function (e) {
    e.preventDefault();

    let otpValue = Array.from(otpInputs).map(input => input.value).join('');

    if (otpValue.length === 6) {
        fetch('/api/otp-verification', {
            method: 'POST',
            body: JSON.stringify({
                otp: otpValue
            })
        }).then(response => response.json()).then(response => {
            if (response) {
                otpActivationContainer.style.display = "none";
                responseOTP.classList.remove('hidden');
                lottiePlayer.stop();
                lottiePlayer.play();

                setTimeout(() => {
                    window.location.href = "/comptes/modification?tab=securite";
                }, 3000);
            } else {
                otpCodeError.classList.remove('hidden');
                otpCodeError.innerHTML = "Le code fourni est incorrect.";
            }
        });
    } else {
        otpCodeError.classList.remove('hidden');
        otpCodeError.innerHTML = "Veuillez entrer un code à 6 chiffres.";
    }
});
