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
        print(message);
        formatPasswordInput(input, message);
    })
})
