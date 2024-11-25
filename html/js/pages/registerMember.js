document.addEventListener("DOMContentLoaded", () => {
    const switchCondition = document.getElementById("switch-condition-utilisation");
    const submitButton = document.querySelector("button[type='submit']");

    const updateButtonState = () => {
        if (switchCondition.checked) {
            submitButton.disabled = false;
            submitButton.classList.remove("opacity-50", "cursor-not-allowed");
        } else {
            submitButton.disabled = true;
            submitButton.classList.add("opacity-50", "cursor-not-allowed");
        }
    };

    switchCondition.addEventListener("change", updateButtonState);

    updateButtonState(); // Initial check
});