//--------------- payment ---------------
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



//--------------- Popup pour l'avatar ---------------
let popupAvatarUpdate = document.getElementById("popupAvatarUpdate");
let avatarUpdate = document.getElementById("avatarUpdate");
let closePopupAvatar = document.getElementById("closePopupAvatar");


avatarUpdate.addEventListener("click", () => {
    popupAvatarUpdate.classList.toggle("hidden");
});

popupAvatarUpdate.addEventListener("click", (event) => {
    let popupContent = popupAvatarUpdate.querySelector('.popup-content');
    if (!popupContent.contains(event.target)) {
        popupAvatarUpdate.classList.add("hidden");
    }
});

closePopupAvatar.addEventListener("click", (event) => {
    popupAvatarUpdate.classList.add("hidden");
});



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

