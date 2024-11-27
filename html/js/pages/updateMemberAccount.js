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
// Pop up
// ---------------------------------------------------------------------------------------------- //

let popupAvatarUpdate = document.getElementById("popupAvatarUpdate");
let popupSaveUpdate = document.getElementById("popupSaveUpdate");
let avatarUpdate = document.getElementById("avatarUpdate");
let saveUpdate = document.getElementById("saveUpdate");
const popupContent = document.querySelector(".popup-content");

avatarUpdate.addEventListener("click", () => {
    popupAvatarUpdate.classList.toggle("hidden");
});

saveUpdate.addEventListener("click", () => {
    popupSaveUpdate.classList.toggle("hidden");
});

popupAvatarUpdate.addEventListener("click", (event) => {
    if (!popupContent.contains(event.target)) {
        popupAvatarUpdate.classList.add("hidden");
    }
});

saveUpdate.addEventListener("click", (event) => {
    if (!popupContent.contains(event.target)) {
        popupSaveUpdate.classList.add("hidden");
    }
});
