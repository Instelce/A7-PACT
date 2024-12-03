import "../pages/registerProfessional.js";


const buttonPayment = document.getElementById("buttonPayment");
const popupPayment = document.getElementById("popupOfPayment");
const closeButton = document.getElementById("closePopup");


buttonPayment.addEventListener("click", ()=>{
    popupPayment.classList.toggle("hidden");
})

closeButton.addEventListener('click', () => {
    popupPayment.classList.add('hidden');
});

