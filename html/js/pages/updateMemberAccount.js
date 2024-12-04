// ---------------------------------------------------------------------------------------------- //
// Gestion des pop up
// ---------------------------------------------------------------------------------------------- //

// Popup pour l'avatar
let popupAvatarUpdate = document.getElementById("popupAvatarUpdate");
let avatarUpdate = document.getElementById("avatarUpdate");
let closePopupAvatar = document.getElementById("closePopupAvatar");

// Popup pour l'enregistrement des modifications
let saveUpdatePopupTrigger = document.getElementById("saveUpdatePopupTrigger");
let popupSaveUpdate = document.getElementById("popupSaveUpdate");
let closePopupSave = document.getElementById("closePopupSave");

// Popup pour la supression du compte
let popupAccountDelete = document.getElementById("popupAccountDelete");
let accountDelete = document.getElementById("accountDelete");
let closePopupDelete = document.getElementById("closePopupDelete")

// Popup pour la modification du mot de passe
let popupPasswordModify = document.getElementById("popupPasswordModify");
let closePasswordModify = document.getElementById("closePasswordModify");
let passwordModify = document.getElementById("passwordModify");

/////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////
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
/////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////
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
/////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////
accountDelete.addEventListener("click", () => {
    popupAccountDelete.classList.toggle("hidden");
});

popupAccountDelete.addEventListener("click", (event) => {
    if (!popupContent.contains(event.target)) {
        popupAccountDelete.classList.add("hidden");
    }
});

closePopupDelete.addEventListener("click", (event) => {
    popupAccountDelete.classList.add("hidden");
});
/////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////

const phoneInputs = document.querySelectorAll('input[type="tel"]');

function formatPhoneInput(event) {
    const input = event.target;
    let rawValue = input.value.replace(/\D/g, '');
    rawValue = rawValue.substring(0, 10);
    const format = rawValue.replace(/(\d{2})(?=\d)/g, '$1 ').trim();
    input.value = format;
}

phoneInputs.forEach((input) => {
    input.addEventListener('input', formatPhoneInput);
});
