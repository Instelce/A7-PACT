const paymentIsChecked = document.getElementById("check-payment");
const divPayment = document.getElementById("mean-payment");

if (paymentIsChecked && divPayment) {
    paymentIsChecked.addEventListener('change', () => {
        divPayment.classList.toggle('hidden');
    });
}


const payment = document.getElementById("payment");
const contentPayment = document.getElementById("content-payment");

const card = document.getElementById("card");
const contentCard = document.getElementById("content-card");


card.addEventListener("click", ()=> {
    contentCard.classList.toggle('hidden');
    contentPayment.classList.add('hidden');
});

payment.addEventListener("click", ()=> {
    console.log("coucuo")
    contentPayment.classList.toggle('hidden');
    contentCard.classList.add('hidden');
});


const ibanInputs = document.querySelectorAll('#iban');
const cardNumberInputs = document.querySelectorAll('#cardnumber');

function formatIbanInput(event) {
    const input = event.target;
    let rawValue = input.value.replace(/\s+/g, '').toUpperCase();
    rawValue = rawValue.substring(0, 34);
    const formattedValue = rawValue.replace(/(.{4})/g, '$1 ').trim();
    input.value = formattedValue;
}

function formatCardNumberInput(event) {
    const input = event.target;
    let rawValue = input.value.replace(/\D/g, '');
    rawValue = rawValue.substring(0, 16);
    const formattedValue = rawValue.replace(/(.{4})/g, '$1 ').trim();
    input.value = formattedValue;
}

ibanInputs.forEach((input) => {
    input.addEventListener('input', formatIbanInput);
});

cardNumberInputs.forEach((input) => {
    input.addEventListener('input', formatCardNumberInput);
});