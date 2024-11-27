import {createOpinionCard} from "../cards.js";

// -------------------------------------------------------------------------------------------------
// Offers loading
// -------------------------------------------------------------------------------------------------

let offset = 0;
const limit = 5;

const offersContainers = document.querySelector('#offers-container');
const offersLoader = document.querySelector('#offers-loader');

if (offersContainers) {
    const offerObserver = new IntersectionObserver(async () => {
        let account_id = location.href.split('/')[4];
        // Generate params
        let params = new URLSearchParams();
        params.set('limit', limit);
        params.set('offset', offset);
        params.set('professional_id', account_id);

        // Fetch offers
        let response = await fetch(`/api/offers?${params.toString()}`);
        let offers = await response.json();

        for (const offer of offers) {
            offersContainers.insertBefore(createOfferCard(offer), offersLoader);
        }
        offset += limit;
    }, {
        root: null,
        rootMargin: '0px',
        threshold: 1.
    })
    offerObserver.observe(offersLoader);
}

function createOfferCard(offer) {
    let card = document.createElement('a');
    card.href = `/offres/${offer.id}`;
    card.innerHTML = `<a href="${card.href}">
        <article class="research-card">
            <div class="research-card--photo">
                <img alt="photo d'article" src="${offer.photos[0]}"/>
            </div>

            <div class="research-card--body">
                <header>
                    <h2 class="research-card--title">${offer.title} </h2>
                    <p>${translateCategory(offer.category)} par <a href="/comptes/${offer.professional_id}"
                                                            class="underline"> ${offer.profesionalUser.denomination} </a>
                    </p>
                </header>

                <p class="summary">${offer.summary}</p>

                <div class="flex gap-2 mt-auto pt-4">
                    <a href="${card.href}" class="button gray w-full spaced">Itinéraire<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map"><path d="M14.106 5.553a2 2 0 0 0 1.788 0l3.659-1.83A1 1 0 0 1 21 4.619v12.764a1 1 0 0 1-.553.894l-4.553 2.277a2 2 0 0 1-1.788 0l-4.212-2.106a2 2 0 0 0-1.788 0l-3.659 1.83A1 1 0 0 1 3 19.381V6.618a1 1 0 0 1 .553-.894l4.553-2.277a2 2 0 0 1 1.788 0z"/><path d="M15 5.764v15"/><path d="M9 3.236v15"/></svg></a>
                    <a href="${card.href}" class="button blue w-full spaced">Voir plus<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right"><path d="m9 18 6-6-6-6"/></svg></a>
                </div>
            </div>
        </article>
    </a>`
//    console.log(offer);
    return card;
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

// -------------------------------------------------------------------------------------------------
// Opinions loading
// -------------------------------------------------------------------------------------------------

const opinionsContainers = document.querySelector('#opinions-container');
const opinionsLoader = document.querySelector('#opinions-loader');

if (opinionsContainers) {
    const opinionObserver = new IntersectionObserver(async () => {
        let account_id = location.href.split('/')[4];

        // Generate params
        let params = new URLSearchParams();
        params.set('limit', limit);
        params.set('offset', offset);
        params.set('account_id', account_id);

        // Fetch opinions
        let response = await fetch(`/api/opinions?${params.toString()}`);
        let opinions = await response.json();

        for (const opinion of opinions) {
            opinionsContainers.insertBefore(createOpinionCard(opinion), opinionsLoader);
        }
        offset += limit;
    }, {
        root: null,
        rootMargin: '0px',
        threshold: 1.
    })

    opinionObserver.observe(opinionsLoader);
}


