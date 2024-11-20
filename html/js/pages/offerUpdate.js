import '../photoUpload.js';

// -------------------------------------------------------------------------- //
// Toggle visibility of period FIELDS
// -------------------------------------------------------------------------- //

let periodFields = document.querySelector('#period-fields');
let periodSelect = document.querySelector('#switch-period');

if (periodSelect) {
    let periodInput = periodFields.querySelectorAll('input');

    periodSelect.addEventListener('change', (e) => {
        periodFields.classList.toggle('hidden');
        periodInput.forEach(input => {
            input.required = !input.required;
        })
    })
}

// -------------------------------------------------------------------------- //
// Calculate prices
// -------------------------------------------------------------------------- //

let typeStandardInput = document.querySelector('#type-standard');
let typePremiumInput = document.querySelector('#type-premium');

let priceWithoutOption = document.querySelector('#price-without-option');
let priceWithOption = document.querySelector('#price-with-option');
let priceSubTotal = document.querySelector('#price-subtotal');

let offerPrices = {
    "standard": 4.98,
    "premium": 7.98,
}

let optionPrices = {
    "no": 0,
    "en-relief": 2.98,
    "a-la-une": 4.98,
}

if (typeStandardInput && typePremiumInput) {
    typeStandardInput.addEventListener('input', updatePrices);
    typePremiumInput.addEventListener('input', updatePrices);
    noOption.addEventListener('input', updatePrices);
    enreliefOption.addEventListener('input', updatePrices);
    alauneOption.addEventListener('input', updatePrices);
}

function updatePrices() {
    let type = typeStandardInput.checked ? "standard" : "premium";
    let option = noOption.checked ? "no" : enreliefOption.checked ? "en-relief" : "a-la-une";

    priceWithoutOption.textContent = `${offerPrices[type]} €`;
    priceWithOption.textContent = `${(offerPrices[type] + optionPrices[option]).toFixed(2)} €`;
    priceSubTotal.textContent = `${(offerPrices[type] + optionPrices[option]).toFixed(2)} €`;
}

let photosContainer = document.querySelector('#photos');
let photos = photosContainer.querySelectorAll('.uploaded-image-card')

photos.forEach(photo => {
    let deleteButton = photo.querySelector('.photo-remove');
    let imageId = photo.querySelector('.image-id')

    deleteButton.addEventListener('click', (e) => {
        e.target.closest('.uploaded-image-card').remove();

        console.log(imageId.value);

        let deleteInputTracker = document.createElement('input');
        deleteInputTracker.hidden = true;
        deleteInputTracker.name = "deleted-photos[]";
        deleteInputTracker.value = imageId.value;

        console.log(deleteInputTracker.value);

        photosContainer.appendChild(deleteInputTracker);
    })
})
// removeButtons.forEach(button => {
//     button.addEventListener('click', (e) => {
//         e.target.closest('.uploaded-image-card').remove();

//         let imageId =

//             let deleteInputTracker = document.createElement('input');
//         deleteInputTracker.hidden = true;
//         deleteInputTracker.name = "deleted-photos[]";
//         deleteInputTracker.value =
//                 })
// })