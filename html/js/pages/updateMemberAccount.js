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
