document.addEventListener("DOMContentLoaded", () => {
    const switchCondition = document.getElementById("switch-condition-utilisation");
    const submitButton = document.querySelector("button[type='submit']");

    const updateButtonState = () => {
        if (switchCondition.checked) {
            submitButton.disabled = false;
            submitButton.classList.remove("cursor-not-allowed");
        } else {
            submitButton.disabled = true;
            submitButton.classList.add("cursor-not-allowed");
        }
    };

    switchCondition.addEventListener("change", updateButtonState);

    updateButtonState();
});

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