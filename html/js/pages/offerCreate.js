import '../address.js';
import '../photoUpload.js';

// -------------------------------------------------------------------------- //
// Load option date picker
// -------------------------------------------------------------------------- //

const noOption = document.querySelector('#type-no');
const enreliefOption = document.querySelector('#type-in-relief');
const alauneOption = document.querySelector('#type-a-la-une');
const datesContainer = document.querySelector('#option-dates');

if (noOption && enreliefOption && alauneOption) {
    noOption.addEventListener('input', updateDatesVisibility);
    enreliefOption.addEventListener('input', updateDatesVisibility);
    alauneOption.addEventListener('input', updateDatesVisibility);
}

function updateDatesVisibility() {
    if (noOption.checked) {
        datesContainer.classList.add('hidden');
    } else {
        datesContainer.classList.remove('hidden');
    }
}


// -------------------------------------------------------------------------- //
// Containers of additional data
// -------------------------------------------------------------------------- //

const complemantarySections = document.querySelectorAll(".complementary-section");
const categoryNotSelected = document.querySelector('#category-no-selected');
const periodSection = document.querySelector('#period-section');

const priceSection = document.querySelector('#price-section');
const rangePriceInput = document.querySelector('#restaurant-range-price');
const minimumPriceInput = document.querySelector('#offer-minimum-price');

// -------------------------------------------------------------------------- //
// Load tags in order to selected category
// -------------------------------------------------------------------------- //

let tags = {
    'restaurant': ['Française', 'Fruit de mer', 'Plastique', 'Italienne', 'Indienne', 'Gastronomique', 'Restauration rapide', 'Crêperie'],
    'others': ['Culturel', 'Gastronomie', 'Patrimoine', 'Musée', 'Histoire', 'Atelier', 'Urbain', 'Musique', 'Nature', 'Famille', 'Plein air', 'Cirque', 'Sport', 'Son et lumière', 'Nautique', 'Humour'],
}

let tagsContainer = document.querySelector('#tags');
let categoryInput = document.querySelector('input#category');

categoryInput.addEventListener('change', (e) => {
    // Remove all tags
    tagsContainer.innerHTML = '';

    // Add tags for the selected category
    let key = categoryInput.value === 'restaurant' ? 'restaurant' : 'others';
    tags[key].forEach(tag => {
        let tagElement = document.createElement('div');
        tagElement.innerHTML =
            tagsContainer.innerHTML += `
            <div class="flex items-center gap-1">
                <input type="checkbox" class="checkbox checkbox-normal pro" name="tags[]" value="${tag}" id="${tag}">
                <label for="${tag}">${tag}</label>
            </div>
        `;
    })


    // -------------------------------------------------------------------------- //
    // Toggle SCHEDULE section visibility in order to selected category
    // -------------------------------------------------------------------------- //

    let scheduleSection = document.querySelector('#schedules-section');

    if (categoryInput.value === 'restaurant' || categoryInput.value === 'activity' || categoryInput.value === 'attraction-park') {
        scheduleSection.classList.remove('hidden');
    } else {
        scheduleSection.classList.add('hidden');
    }


    // -------------------------------------------------------------------------- //
    // Toggle visibility of additional information
    // -------------------------------------------------------------------------- //

    complemantarySections.forEach(section => {
        let inputs = section.querySelectorAll('input');
        if (section.getAttribute('data-category') === categoryInput.value) {
            section.classList.remove('hidden');
            for (let input of inputs) {
                if (!input.classList.contains('switch')) {
                    input.required = true;
                }
            }
        } else {
            section.classList.add('hidden');
            for (let input of inputs) {
                input.required = false;
            }
        }
    })

    categoryNotSelected.classList.add('hidden');


    // -------------------------------------------------------------------------- //
    // Toggle visibility of period section
    // -------------------------------------------------------------------------- //

    if (categoryInput.value === 'visit' || categoryInput.value === 'show') {
        periodSection.classList.remove('hidden');
    }

    if (categoryInput.value !== 'restaurant') {
        minimumPriceInput.required = true;
        priceSection.classList.remove('hidden');
    } else {
        minimumPriceInput.required = false;
        priceSection.classList.add('hidden');
    }
})


// -------------------------------------------------------------------------- //
// Toggle visibility of period FIELDS
// -------------------------------------------------------------------------- //

let periodFields = document.querySelector('#period-fields');
let switchPeriod = document.querySelector('#switch-period');

switchPeriod.addEventListener('input', (e) => {
    if (switchPeriod.checked) {
        periodFields.classList.remove('hidden');
        periodFields.innerHTML = `
            <x-input>
                <p slot="label">Début de la période</p>
                <input slot="input" type="date" name="period-start" required>
            </x-input>
            <x-input>
                <p slot="label">Fin de la période</p>
                <input slot="input" type="date" name="period-end" required>
            </x-input>
        `
    } else {
        periodFields.innerHTML = '';
    }
})


// -------------------------------------------------------------------------- //
// Fill the SCHEDULE table with all the days of the week
// -------------------------------------------------------------------------- //

let scheduleTable = document.querySelector('#schedule-table');
let days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
let scheduleTableBody = scheduleTable.querySelector('tbody#schedules-rows');

days.forEach((day, index) => {
    scheduleTableBody.innerHTML += `
        <tr>
            <td>${day}</td>
            <td>
                <div class="flex justify-center">
                    <input type="time" name="schedules[${index}][open]">
                </div>
            </td>
            <td>
                <div class="flex justify-center">
                    <input type="time" name="schedules[${index}][close]">
                </div>
            </td>
        </tr>
    `;
})


// -------------------------------------------------------------------------- //
// Toggle price fields visibility
// -------------------------------------------------------------------------- //

let priceRadioFree = document.querySelector('#price-free');
let priceRadioPaying = document.querySelector('#price-paying');
let priceTable = document.querySelector('#price-fields');

priceRadioFree.addEventListener('input', updatePriceTableVisibility);
priceRadioPaying.addEventListener('input', updatePriceTableVisibility);

function updatePriceTableVisibility() {
    if (priceRadioPaying.checked) {
        priceTable.classList.remove('hidden');
    } else {
        priceTable.classList.add('hidden');
    }
}


// -------------------------------------------------------------------------- //
// Price grid new row
// -------------------------------------------------------------------------- //

let priceTableBody = priceTable.querySelector('tbody#prices-rows');
let priceAddButton = document.querySelector('#add-price-row');

priceAddButton.addEventListener('click', (e) => {
    e.preventDefault();
    priceTableBody.insertAdjacentHTML('beforeend', `
        <tr>
            <td>
                <input id="price-name" name="prices[]" type="text" placeholder="Nom" class="table-input">
            </td>
            <td>
                <input id="price-value" name="prices[]" type="number" placeholder="Prix" class="table-input">
            </td>
        </tr>
    `);
})



// -------------------------------------------------------------------------- //
// Calculate prices
// -------------------------------------------------------------------------- //

let typeStandardInput = document.querySelector('#type-standard');
let typePremiumInput = document.querySelector('#type-premium');

let priceWithoutOption = document.querySelector('#price-without-option');
let priceWithOption = document.querySelector('#price-option');
// let priceSubTotal = document.querySelector('#price-subtotal');
let priceTotal = document.querySelector('#price-total');
let priceTotalMonth = document.querySelector('#price-total-month');
let priceTotalOption = document.querySelector('#price-total-option');

let offerPrices = {
    "standard": 1.67,
    "premium": 3.34,
}

let optionPrices = {
    "no": 0,
    "en-relief": 8.34,
    "a-la-une": 16.68,
}

updatePrices()

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
    priceWithOption.textContent = `${(optionPrices[option]).toFixed(2)} €`;
    // priceSubTotal.textContent = `${(offerPrices[type] + optionPrices[option]).toFixed(2)} €`;

    // Total price with 20% TVA
    priceTotal.textContent = `${(offerPrices[type] * 1.2).toFixed(2)} €`;
    priceTotalMonth.textContent = `soit ${30 * (offerPrices[type] * 1.2)} € TTC / mois`
    priceTotalOption.textContent = `${((optionPrices[option]) * 1.2).toFixed(2)} €`;
}