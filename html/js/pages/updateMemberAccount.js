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

let popup = document.getElementById("popup");
let updateAvatar = document.getElementById("updateAvatar");
const popupContent = document.querySelector(".popup-content");

updateAvatar.addEventListener("click", () => {
    popup.classList.toggle("hidden");
});

popup.addEventListener("click", (event) => {
    if (!popupContent.contains(event.target)) {
        popup.classList.add("hidden");
    }
});
