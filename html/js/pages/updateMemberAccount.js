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


let avatarImage = document.querySelector('.avatar-image');
let avatarInput = document.querySelector('.avatar-input');

avatarInput.addEventListener('change', (event) => {
    let file = event.target.files[0];
    let reader = new FileReader();

    reader.onload = (e) => {
        avatarImage.src = e.target.result;
    }

    reader.readAsDataURL(file);
})