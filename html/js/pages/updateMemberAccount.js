// ---------------------------------------------------------------------------------------------- //
// Gestion du toggle switch
// ---------------------------------------------------------------------------------------------- //

const toggleKey = "notificationToggleState";
const toggleSwitch = document.getElementById("switch-notification");

document.addEventListener("DOMContentLoaded", () => {
    const savedState = localStorage.getItem(toggleKey);

    if (savedState !== null) {
        toggleSwitch.checked = savedState === "true";
    }
});

toggleSwitch.addEventListener("change", () => {
    localStorage.setItem(toggleKey, toggleSwitch.checked);
});

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
//let popupAccountDelete = document.getElementById("popupAccountDelete");
let accountDelete = document.getElementById("accountDelete");
//let closeAccountDelete = document.getElementById("closeAccountDelete");

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
/*
accountDelete.addEventListener("click", () => {
    popupAccountDelete.classList.toggle("hidden");
});

popupAccountDelete.addEventListener("click", (event) => {
    if (!popupContent.contains(event.target)) {
        popupAccountDelete.classList.add("hidden");
    }
});
*/

// closeAccountDelete.addEventListener("click", (event) => {
//     if (!popupContent.contains(event.target)) {
//         popupAccountDelete.classList.add("hidden");
//     }
// });
/////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////
passwordModify.addEventListener("click", () => {
    popupPasswordModify.classList.toggle("hidden");
});

popupPasswordModify.addEventListener("click", (event) => {
    if (!popupContent.contains(event.target)) {
        popupPasswordModify.classList.add("hidden");
    }
});

/*
closePasswordModify.addEventListener("click", (event) => {
    if (!popupContent.contains(event.target)) {
        popupPasswordModify.classList.add("hidden");
    }
});
*/
/////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////
