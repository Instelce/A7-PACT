document.addEventListener("DOMContentLoaded", () => {
    const switch1 = document.getElementById("switch-period1");
    const switch2 = document.getElementById("switch-period2");
    const submitButton = document.querySelector("button[type='submit']");

    const ButtonState = () => {
        if (switch1.checked) {
            submitButton.disabled = false;
            submitButton.classList.remove("opacity-50", "cursor-not-allowed");
        } else {
            submitButton.disabled = true;
            submitButton.classList.add("opacity-50", "cursor-not-allowed");
        }
    };

    switch1.addEventListener("change", ButtonState);

    ButtonState();
});
