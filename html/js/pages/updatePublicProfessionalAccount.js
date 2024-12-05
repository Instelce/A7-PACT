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

