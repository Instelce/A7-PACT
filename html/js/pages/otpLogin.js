/*
const otpInput = document.querySelector('#otpLogin');

function formatOtpInput(event) {
    const input = event.target;
    let rawValue = input.value.replace(/\s+/g, '').toUpperCase();
    rawValue = rawValue.substring(0, 6);
    const formattedValue = rawValue.replace(/(.{3})/g, '$1 ').trim();
    input.value = formattedValue;
}

otpInput.addEventListener('input', formatOtpInput);
 */

const inputs = document.querySelectorAll('.otp-box');
const hiddenInput = document.querySelector('#otpHidden');

inputs.forEach((input, index) => {
    input.addEventListener('input', (e) => {
        const value = e.target.value.replace(/\D/g, '');
        e.target.value = value;

        if (value && index < inputs.length - 1) {
            inputs[index + 1].focus();
        }

        updateHiddenInput();
    });

    input.addEventListener('keydown', (e) => {
        if (e.key === "Backspace" && !e.target.value && index > 0) {
            inputs[index - 1].focus();
        }
    });
});

function updateHiddenInput() {
    hiddenInput.value = Array.from(inputs).map(i => i.value).join('');
}
