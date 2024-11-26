let offset = 0;
const limit = 5;


const offersContainers = document.querySelector('#offers-container');
const offersLoader = document.querySelector('#offers-loader');

const observer = new IntersectionObserver(async () => {
    let account_id = location.href.split('/')[4];

    // Generate params
    let params = new URLSearchParams();
    params.set('limit',limit);
    params.set('offset',offset);
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

observer.observe(offersLoader);


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
                                                            class="underline"> ${offer} </a>
                    </p>
                </header>

                <p class="summary">${offer.summary}</p>

                <div class="flex gap-2 mt-auto pt-4">
                    <a href="" class="button gray w-full spaced">Itinéraire<i data-lucide="map"></i></a>
                    <a href="" class="button blue w-full spaced">Voir plus<i data-lucide="chevron-right"></i></a>
                </div>
            </div>
        </article>
    </a>`

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