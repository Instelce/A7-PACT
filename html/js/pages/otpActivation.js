let otpActivationForm = document.getElementById("otpActivation");
let otpForm = document.getElementById("otpForm");
let responseOTP = document.getElementById("responseOTP");
let otpCodeError = document.getElementById("otpCodeError");
let lottiePlayer = document.getElementById("lottiePlayer");

otpActivationForm.addEventListener("submit", function (e) {
    e.preventDefault();
    let formData = new FormData(e.target);

    fetch('/api/otp-verification', {
        method: 'POST',
        body: JSON.stringify({
            otp: formData.get('otp')
        })
    }).then(response => response.json()).then(response => {
        if (response) {
            otpForm.style.display = "none";
            responseOTP.classList.remove('hidden');
            lottiePlayer.stop();
            lottiePlayer.play();

            setTimeout(() => {
                window.location.href = "/comptes/modification?tab=securite";
            }, 3000);
        } else {
            otpCodeError.classList.remove('hidden');
            otpCodeError.innerHTML = "Le code fourni est erroné";
        }
    });
});
