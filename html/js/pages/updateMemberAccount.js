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

document.addEventListener("DOMContentLoaded", () => {
    const passwordCondition = document.getElementById("password-condition-utilisation");
    const saveButton = document.querySelector("button[type='submit']");

    const updateSaveButton = () => {
        if (passwordCondition.checked) {
            saveButton.disabled = false;
            saveButton.classList.remove("opacity-50", "cursor-not-allowed");
        } else {
            saveButton.disabled = true;
            saveButton.classList.add("opacity-50", "cursor-not-allowed");
        }
    };

    passwordCondition.addEventListener("change", updateSaveButton);

    updateSaveButton();
});

// ---------------------------------------------------------------------------------------------- //
// Gestion des pop up
// ---------------------------------------------------------------------------------------------- //

let popupAvatarUpdate = document.getElementById("popupAvatarUpdate");
let popupSaveUpdate = document.getElementById("popupSaveUpdate");
//let popupAccountDelete = document.getElementById("popupAccountDelete");
let popupPasswordModify = document.getElementById("popupPasswordModify");

let avatarUpdate = document.getElementById("avatarUpdate");
let saveUpdatePopupTrigger = document.getElementById("saveUpdatePopupTrigger");
let accountDelete = document.getElementById("accountDelete");
let passwordModify = document.getElementById("passwordModify");


//let closeAccountDelete = document.getElementById("closeAccountDelete");
let closePasswordModify = document.getElementById("closePasswordModify");

const popupContent = document.querySelector(".popup-content");

avatarUpdate.addEventListener("click", () => {
    popupAvatarUpdate.classList.toggle("hidden");
});

popupAvatarUpdate.addEventListener("click", (event) => {
    let popupContent = popupAvatarUpdate.querySelector('.popup-content');
    if (!popupContent.contains(event.target)) {
        popupAvatarUpdate.classList.add("hidden");
    }
});

saveUpdatePopupTrigger.addEventListener("click", () => {
    popupSaveUpdate.classList.toggle("hidden");
});

popupSaveUpdate.addEventListener("click", (event) => {
    let popupContent = popupSaveUpdate.querySelector('.popup-content');
    if (!popupContent.contains(event.target)) {
        popupSaveUpdate.classList.add("hidden");
    }
});

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

passwordModify.addEventListener("click", () => {
    popupPasswordModify.classList.toggle("hidden");
    // Application::$app->mailer->send("test@example.com", "Test", "welcome", ["pseudo" => "Célestin"]);
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

// closeAccountDelete.addEventListener("click", (event) => {
//     if (!popupContent.contains(event.target)) {
//         popupAccountDelete.classList.add("hidden");
//     }
// });