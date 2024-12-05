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


function formatPasswordInput(inputElement, messageElement) {
    const letter = messageElement.querySelector(".letter");
    const capital = messageElement.querySelector(".capital");
    const number = messageElement.querySelector(".number");
    const special = messageElement.querySelector(".special");
    const length = messageElement.querySelector(".length");

    const lowerCaseLetters = /[a-z]/g;
    const upperCaseLetters = /[A-Z]/g;
    const numbers = /[0-9]/g;
    const specials = /[!@#$%^&*(),.?":{}|<>]/g;
    const minLength = 12;


    if (messageElement) {
        inputElement.addEventListener("input", () => {
            console.log("test");

            let value = inputElement.value;

            const hasLowerCase = value.match(lowerCaseLetters);
            const hasUpperCase = value.match(upperCaseLetters);
            const hasNumber = value.match(numbers);
            const hasSpecialChar = value.match(specials);
            const isLongEnough = value.length >= minLength;

            updateRequirement(hasLowerCase, letter);
            updateRequirement(hasUpperCase, capital);
            updateRequirement(hasNumber, number);
            updateRequirement(hasSpecialChar, special);
            updateRequirement(isLongEnough, length);
        });
    }
}

function updateRequirement(condition, element) {
    if (condition) {
        element.classList.remove("invalid");
        element.classList.add("valid");
    } else {
        element.classList.remove("valid");
        element.classList.add("invalid");
    }
}

function print(messageElement){
    messageElement.classList.remove("hidden");
}

let passwordInputs = document.querySelectorAll(".password-check");
passwordInputs.forEach(container=>{
    let input = container.querySelector("input[type='password']");
    let message = container.querySelector(".password-requirements");
    input.addEventListener('click',()=>{
        print(message);
    })
    input.addEventListener('input', ()=>{
        formatPasswordInput(input, message);
    })
})