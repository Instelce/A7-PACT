let otpActivationForm = document.getElementById("otpActivation");
let responseOTP = document.getElementById("responseOTP");
let otpQrCode = document.getElementById("otpQrCode");
let lottiePlayer = document.getElementById("lottiePlayer");

otpActivationForm.addEventListener("submit", function (e) {
    e.preventDefault();
    let formData = new FormData(e.target);
    fetch('/api/otp-verification', {
        method: 'POST',
        body: JSON.stringify({
            otp: formData.get('otp')
        })
    }).then(response => response.json()).then(response=>{
        console.log(response)
        if(response){
            responseOTP.classList.remove('hidden');
            lottiePlayer.stop();
            lottiePlayer.play();
            otpActivationForm.style.display = "none"
            otpQrCode.style.display = "none"
            setTimeout(() => {
                window.location.href = "/comptes/modification?tab=securite";
            }, 4000);
        } else {
            responseOTP.innerHTML = "Le code fournie est erroné"
        }
    })
})