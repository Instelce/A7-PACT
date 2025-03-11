class OfferRecentlyConsulted {
    offerIds = [];

    KEY = 'offerRecentlyConsulted';
    MAX_OFFERS = 6;

    constructor() {
    }

    consultOffer(offerId) {
        console.log("Consult", offerId);
        if (!this.offerIds.includes(offerId)) {
            if (this.offerIds.length >= this.MAX_OFFERS) {
                this.offerIds.pop();
            }

            this.offerIds.unshift(offerId);
            this.saveRecentlyConsulted();
        }
    }

    loadRecentlyConsulted() {
        let localData = localStorage.getItem(this.KEY);

        if (localData) {
            this.offerIds = JSON.parse(localData);
        } else {
            localStorage.setItem(this.KEY, "[]");
        }
    }

    saveRecentlyConsulted() {
        localStorage.setItem(this.KEY, JSON.stringify(this.offerIds));
    }
}

export const offerRecentlyConsulted = new OfferRecentlyConsulted();
