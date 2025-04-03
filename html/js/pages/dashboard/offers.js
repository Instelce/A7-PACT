import {getUser} from "../../user.js";

// -------------------------------------------------------------------------------------------------
// Load offers card
// -------------------------------------------------------------------------------------------------

let user;
let loading = false;
let nextMonday = document.querySelector('input#next-monday').value;
let offset = 0;
let limit = 3;
let isLoading = false;

// Filter elements
let searchInput = document.querySelector('#search-input');
let categorySelect = document.querySelector('input#category-select');
let typeSelect = document.querySelector('input#type-select');
let optionSelect = document.querySelector('input#option-select');
let statusSelect = document.querySelector('input#status-select');

let offersContainer = document.querySelector('#offers-container');
let loaderSection = offersContainer.querySelector('.loader-section');

// Create observer to load cards on scroll
const observer = new IntersectionObserver((entries) => {
    if (entries[0].isIntersecting) {
        loadCards();
    }
}, {
    root: null,
    rootMargin: '0px',
    threshold: 1.
})

// Observe loader section
getUser().then(_user => {
    user = _user;
    observer.observe(loaderSection);
});

// Refresh the offers when the input or select change
searchInput.addEventListener('input', debounce(() => {
    preloadReset();
    loadCards();
}, 300))

categorySelect.addEventListener('change', () => {
    preloadReset();
    loadCards();
})

typeSelect.addEventListener('change', () => {
    preloadReset();
    loadCards();
})

optionSelect.addEventListener('change', () => {
    preloadReset();
    loadCards();
})

statusSelect.addEventListener('change', () => {
    preloadReset();
    loadCards();
})

// -------------------------------------------------------------------------------------------------
// Functions
// -------------------------------------------------------------------------------------------------

function preloadReset() {
    offset = 0;
    loading = true;
    removeCards();
}

function loadCards() {
    if (isLoading) return;
    isLoading = true;

    fetchOffers()
        .then(offers => {
            // Disable loader section
            if (offers.length < limit) {
                loaderSection.classList.add('hidden');
            }

            if (offers.length === limit) {
                loaderSection.classList.remove('hidden');
            }

            for (let offer of offers) {
                offersContainer.insertBefore(createCard(offer), loaderSection);
            }

            offset += limit;
        })
        .finally(() => {
            isLoading = false;
        });
}

async function fetchOffers() {
    let params = new URLSearchParams();

    params.set('limit', limit.toString());
    params.set('offset', offset.toString());
    params.set('professional_id', user.account_id.toString());
    params.set('q', searchInput.value.trim());

    if (categorySelect.value !== 'all')
        params.set('category', categorySelect.value);
    if (typeSelect.value !== 'all')
        params.set('type', typeSelect.value);
    if (optionSelect.value !== 'all')
        params.set('option', optionSelect.value);
    if (statusSelect.value !== 'all') {
        params.set('status', statusSelect.value);
    }

    return fetch(`/api/offers?${params.toString()}`).then(r => {
        loading = false;
        return r.json();
    });
}

function createCard(offer) {
    let card = document.createElement('article');
    card.classList.add('offer-card');

    let specific = offer.specific;

    // Calculate price
    let price = '';
    if (offer.minimum_price === null || offer.minimum_price === 0) {
        price = 'Gratuit';
    } else {
        price = 'A partir de ' + offer.minimum_price + ' €';
    }

    if (offer.category === 'restaurant') {
        price = Array(offer.minimum_price).fill('€').join('');
    }

    card.innerHTML = `
            <div>
                <div class="image-container">
                    <img src="${offer.photos.at(0)}" onerror="this.src = 'https://placehold.co/100'">
                </div>
                <div class="mt-4 flex gap-2 items-center justify-center">
                    <span class="w-4 h-4 block rounded-full ${offer.offline ? `bg-red-500` : `bg-green-500`}"></span>
                    <p>${offer.offline ? `Hors ligne` : `En ligne`}</p>
                </div>
            </div>

            <div class="card-body">
                <header>
                    <h3 class="title"><a href="/offres/${offer.id}">${offer.title}</a></h3>
                    <span
                        class="badge ${offer.type === 'standard' ? 'blue' : 'yellow'}">${capitalize(offer.type)}</span>
                </header>
        
                <p class="mt-3">${offer.summary}</p>
        
                <div class="flex flex-col gap-2 mt-4">
                    <p class="text-gray-4 flex items-center gap-2">
                        ${capitalize(translateCategory(offer.category))}
                        <span class="dot"></span> ${price}
                        <span class="dot"></span> ${offer.opinion_count} avis
                    </p>
                    <p class="text-gray-4">Mis à jour
                        le ${formatDate(offer.updated_at)}</p>
                </div>
        
                <!-- Option -->
                ${offer.subscription !== null ? `
                    <div class="card-option">
                        <div>
                            <p class="flex gap-1">Avec l'option <span
                                    class="underline">${formatOptionType(offer.subscription.option.type)}</span>
                            </p>
                            <p class="text-gray-4">
                                Du ${formatDate(offer.subscription.launch_date)}
                                au ${formatDate(offer.subscription.end_date)}
                            </p>
                        </div>
                        <button class="button gray only-icon no-border">
                            <i data-lucide="pen-line"></i>
                        </button>
                    </div>
                ` : `
                    <div class="card-option">
                        <div>
                            <p class="flex gap-1">Sans option</p>
                        </div>
                        <button class="link pro" id="add-option-button">
                            Ajouter une option
                        </button>
                    </div>
        
                    <!-- Form to add an option to the offer -->
                    <form method="post" id="add-option-form" class="flex flex-col gap-2 mt-6 hidden">
                        <input type="hidden" name="form-name" value="add-option">
                        <input type="hidden" name="offer_id" value="${offer.id}">
        
                        <!-- Option type -->
                        <div class="option-choices grid grid-cols-2 gap-2">
                            <label for="option-relief" class="button gray">
                                En relief
                                <input id="option-relief" type="radio" name="type" value="en_relief" checked>
                            </label>
                            <label for="option-a-la-une" class="button gray">
                                A la une
                                <input id="option-a-la-une" type="radio" name="type" value="a_la_une">
                            </label>
                        </div>
        
                        <!-- Other fields, launch date and week number -->
                        <div id="option-dates" class="flex gap-4 mt-2 w-full">
                            <x-input>
                                <p slot="label">Date de lancement</p>
                                <input slot="input" type="date" step="7" name="launch_date" value="${nextMonday}" min="${nextMonday}">
                                <p slot="helper">L'option prendra effet en début de semaine</p>
                            </x-input>
                            <x-input>
                                <p slot="label">Nombre de semaine</p>
                                <input slot="input" type="number" name="duration" max="4"
                                       min="1" value="1">
                            </x-input>
                        </div>
        
                        <!-- Form buttons -->
                        <div class="flex gap-2 mt-2">
                            <button class="button purple w-full">Ajouter</button>
                            <button type="button" class="button gray" id="close-option-form">Annuler</button>
                        </div>
                    </form>
                `}
            </div>
        
            <div class="flex flex-col gap-2">
                <a href="/offres/${offer.id}/modification"
                   class="button purple fit mb-2" title="Avis non lu">
                    <!-- <i data-lucide="pen"></i>-->
                    Modifier
                </a>
                <a href="/dashboard/avis?filter=non-lu" class="button purple fit"
                        title="Avis non lu">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-message-square-dot"><path d="M11.7 3H5a2 2 0 0 0-2 2v16l4-4h12a2 2 0 0 0 2-2v-2.7"/><circle cx="18" cy="6" r="3"/></svg>
                    ${offer.no_read_opinion_count}
                </a>
                <!-- Other buttons are in the bottom of the page -->
            </div>
    `

    if (!offer.subscription) {
        let cardOption = card.querySelector('.card-option');
        let addOptionButton = card.querySelector('#add-option-button');
        let addOptionForm = card.querySelector('#add-option-form');
        let closeOptionForm = card.querySelector('#close-option-form');

        addOptionButton.addEventListener('click', function () {
            addOptionForm.classList.remove('hidden');
            cardOption.classList.add('!hidden');
        });

        closeOptionForm.addEventListener('click', function () {
            addOptionForm.classList.add('hidden');
            cardOption.classList.remove('!hidden');
        })
    }

    return card;
}

function removeCards() {
    let cards = offersContainer.querySelectorAll('.offer-card');
    for (let card of cards) {
        card.remove();
    }
}


// -------------------------------------------------------------------------------------------------
// Utils
// -------------------------------------------------------------------------------------------------

function formatDate(date) {
    return new Date(date).toLocaleDateString('fr-FR', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

function formatOptionType(type) {
    return type.replaceAll('_', ' ');
}

function translateCategory(category) {
    switch (category) {
        case 'attraction_park':
            return 'parc d\'attraction';
        case 'visit':
            return 'visite';
        case 'restaurant':
            return 'restaurant';
        case 'activity':
            return 'activité';
        case 'show':
            return 'spectacle';
    }
}

function capitalize(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

function debounce(func, wait) {
    let timeout;
    return function () {
        const context = this;
        const args = arguments;
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(context, args), wait);
    };
}

// <a href="/dashboard/avis" className="button gray fit"
//    title="Avis non répondu">
//     <i data-lucide="message-square-more"></i>
//     ${offer.opinion_count}
// </a>
// <a href="/dashboard/avis" className="button gray fit"
//    title="Avis blacklisté">
//     <i data-lucide="ban"></i>
//     <?php echo 0 ?>
// </a>