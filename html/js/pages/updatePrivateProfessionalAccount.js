//--------------- payment ---------------
import "./registerProfessional.js";


//--------------- Popup pour l'enregistrement des modifications ---------------
const saveUpdatePopupTrigger = document.getElementById("saveUpdatePopupTrigger");
const popupSaveUpdate = document.getElementById("popupSaveUpdate");
const closePopupSave = document.getElementById("closePopupSave");


saveUpdatePopupTrigger.addEventListener("click", () => {
    popupSaveUpdate.classList.toggle("hidden");
});


popupSaveUpdate.addEventListener("click", (event) => {
    let popupContent = popupSaveUpdate.querySelector('.popup-content');
    if (!popupContent.contains(event.target)) {
        popupSaveUpdate.classList.add("hidden");
    }
});

closePopupSave.addEventListener("click", (event) => {
    popupSaveUpdate.classList.add("hidden");
});


//--------------- Popup pour l'enregistrement des modifications du paiement ---------------
const saveUpdatePopupTriggerPayment = document.getElementById("saveUpdatePopupTriggerPayment");
const popupSaveUpdatePayment = document.getElementById("popupSaveUpdatePayment");
const closePopupSavePayment = document.getElementById("closePopupSavePayment");


saveUpdatePopupTriggerPayment.addEventListener("click", () => {
    popupSaveUpdatePayment.classList.toggle("hidden");
});


popupSaveUpdatePayment.addEventListener("click", (event) => {
    let popupContent = popupSaveUpdatePayment.querySelector('.popup-content');
    if (!popupContent.contains(event.target)) {
        popupSaveUpdatePayment.classList.add("hidden");
    }
});

closePopupSavePayment.addEventListener("click", (event) => {
    popupSaveUpdatePayment.classList.add("hidden");
});
