import "../components/Payment.js";

const isAsso = document.getElementById("asso");
const divAsso = document.getElementById("siren");

if (isAsso && divAsso) {
    isAsso.addEventListener('change', () => {
        if (isAsso.checked) {
            divAsso.classList.remove('hidden');
        } else {
            divAsso.classList.add('hidden');
        }
    });
}

document.addEventListener("DOMContentLoaded", () => {
    const conditionsPublic = document.getElementById("switch-cond-public");
    const conditionsPrivate = document.getElementById("switch-cond-private");
    const buttonPublic = document.getElementById("submitFormProPublic");
    const buttonPrivate = document.getElementById("submitFormProPrivate");

    const ButtonStatePublic = () => {
        if (conditionsPublic.checked) {
            buttonPublic.disabled = false;
            buttonPublic.classList.remove("opacity-50", "cursor-not-allowed");
        } else {
            buttonPublic.disabled = true;
            buttonPublic.classList.add("opacity-50", "cursor-not-allowed");
        }
    };

    const ButtonStatePrivate = () => {
        if (conditionsPrivate.checked) {
            buttonPrivate.disabled = false;
            buttonPrivate.classList.remove("opacity-50", "cursor-not-allowed");
        } else {
            buttonPrivate.disabled = true;
            buttonPrivate.classList.add("opacity-50", "cursor-not-allowed");
        }
    };

    conditionsPublic.addEventListener("change", ButtonStatePublic);
    ButtonStatePublic();

    conditionsPrivate.addEventListener("change", ButtonStatePrivate);
    ButtonStatePrivate();
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
