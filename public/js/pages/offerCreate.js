

// -------------------------------------------------------------------------- //
// Load option date picker
// -------------------------------------------------------------------------- //

const noOption = document.querySelector('#type-no');
const enreliefOption = document.querySelector('#type-in-relief');
const alauneOption = document.querySelector('#type-a-la-une');
const datesContainer = document.querySelector('#option-dates');

noOption.addEventListener('input', updateDatesVisibility);
enreliefOption.addEventListener('input', updateDatesVisibility);
alauneOption.addEventListener('input', updateDatesVisibility);

function updateDatesVisibility() {
    if (noOption.checked) {
        datesContainer.classList.add('hidden');
    } else {
        datesContainer.classList.remove('hidden');
    }
}

// -------------------------------------------------------------------------- //
// Load tags in order to selected category
// -------------------------------------------------------------------------- //

let tags = {
    'restaurant': ['Française', 'Fruit de mer', 'Plastique', 'Italienne', 'Indienne', 'Gastronomique', 'Restauration rapide', 'Crêperie'],
    'activity': ['Culturel', 'Gastronomie', 'Patrimoine', 'Musée', 'Histoire', 'Atelier', 'Urbain', 'Musique', 'Nature', 'Famille', 'Plein air', 'Cirque', 'Sport', 'Son et lumière', 'Nautique', 'Humour'],
}

let tagsContainer = document.querySelector('#tags');
let categoryInput = document.querySelector('input#category');

categoryInput.addEventListener('change', (e) => {
    // Remove all tags
    tagsContainer.innerHTML = '';

    // Add tags for the selected category
    if (e.target.value === 'restaurant' || e.target.value === 'activity') {
        tags[categoryInput.value].forEach(tag => {
            let tagElement = document.createElement('div');
            tagElement.innerHTML =
            tagsContainer.innerHTML += `
                <div class="flex items-center gap-1">
                    <input type="checkbox" class="checkbox" name="tags[]" value="${tag}" id="${tag}">
                    <label for="${tag}">${tag}</label>
                </div>
            `;
        })
    }
})


// -------------------------------------------------------------------------- //
// Toggle schedule table
// -------------------------------------------------------------------------- //

let scheduleTable = document.querySelector('#schedule-table');
let scheduleSwitch = document.querySelector('#enable-schedule');

scheduleSwitch.addEventListener('change', (e) => {
    scheduleTable.classList.toggle('hidden');
})


// -------------------------------------------------------------------------- //
// Fill the schedule table with all the days of the week
// -------------------------------------------------------------------------- //

let days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
let scheduleTableBody = scheduleTable.querySelector('tbody#schedules-rows');

days.forEach(day => {
    scheduleTableBody.innerHTML += `
        <tr>
            <td>${day}</td>
            <td>
                <div class="flex justify-center">
                    <input type="time" name="schedules[${day}][open]" required>
                </div>
            </td>
            <td>
                <div class="flex justify-center">
                    <input type="time" name="schedules[${day}][close]" required>
                </div>
            </td>
        </tr>
    `;
})


// -------------------------------------------------------------------------- //
// Toggle price table
// -------------------------------------------------------------------------- //

let priceRadioFree = document.querySelector('#price-free');
let priceRadioPaying = document.querySelector('#price-paying');
let priceTable = document.querySelector('#price-table');

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
    console.log('click');
    e.preventDefault();
    priceTableBody.innerHTML += `
        <tr>
            <td>
                <input id="price-name" name="prices[]" type="text" placeholder="Nom" class="table-input">
            </td>
            <td>
                <input id="price-value" name="prices[]" type="number" placeholder="Prix" class="table-input">
            </td>
        </tr>
    `;
})