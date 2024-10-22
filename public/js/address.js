import {debounce} from "./utils/debounce.js";

// Autocompletion for address fields

let address = document.getElementById('address-field');
let addressNumber = document.getElementById('address-number');
let addressStreet = document.getElementById('address-street');
let addressCity = document.getElementById('address-city');
let addressPostalCode = document.getElementById('address-postal-code');
let addressLongitude = document.getElementById('address-longitude');
let addressLatitude = document.getElementById('address-latitude');

const debounceFetchAddress = debounce(fetchAddress, 300);

address.addEventListener('input', function() {
    debounceFetchAddress(address.value);
});

// Listen for the custom selected event
address.addEventListener('selected', (e) => {
    console.log(e.detail);
    let option = e.detail.option;
    addressNumber.value = option.getAttribute('data-number');
    addressStreet.value = option.getAttribute('data-street');
    addressCity.value = option.getAttribute('data-city');
    addressPostalCode.value = option.getAttribute('data-postal-code');
    addressLongitude.value = option.getAttribute('data-lon');
    addressLatitude.value = option.getAttribute('data-lat');
})

function extractAddressParts(address) {
    const parts = address.split(',').map(part => part.trim());

    return {
        number: parts[0],
        street: parts[1],
        city: parts[3],
        postalCode: parts[9]
    };
}

function fetchAddress(query) {
    if (query.length > 1) {
        let list = document.getElementById('address-autocomplete');

        fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(query)}&format=json&addressdetails=1&limit=5&viewbox=-5.05922,48.94041,-1.70290,48.13664&bounded=1`)
            .then(response => response.json())
            .then(data => {
                list.innerHTML = '';
                data.forEach(item => {
                    let option = document.createElement('div');
                    let parts = extractAddressParts(item.display_name);
                    option.innerText = `${parts.number} ${parts.street}, ${item.address.postcode} ${parts.city}`;

                    // Add the address data as attributes
                    option.setAttribute('data-number', item.address.place);
                    option.setAttribute('data-street', item.address.road);
                    option.setAttribute('data-city', item.address.town ? item.address.town : item.address.village);
                    option.setAttribute('data-postal-code', item.address.postcode);
                    option.setAttribute('data-lat', item.lat);
                    option.setAttribute('data-lon', item.lon);

                    list.appendChild(option);

                    option.addEventListener('click', () => {
                        address.value = option.innerText;

                        addressNumber.value = item.address.place ? item.address.place : '';
                        addressStreet.value = item.address.road;
                        addressCity.value = item.address.town;
                        addressPostalCode.value = item.address.postcode;
                        addressLongitude.value = item.lon;
                        addressLatitude.value = item.lat;
                    })
                });
            })
    }
}