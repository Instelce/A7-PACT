
class OfferRecentlyConsulted {
  offerIds = [];

  constructor() {
  }

  consultOffer(offerId) {
    this.offerIds.push(offerId);
    this.saveRecentlyConsulted();
  }

  loadRecentlyConsulted() {
    let localData = localStorage.getItem('offerRecentlyConsulted');

    if (localData) {
      this.offerIds = JSON.parse(localData);
    } else {
      localStorage.setItem('offerRecentlyConsulted', []);
    }
  }

  saveRecentlyConsulted() {
    localStorage.setItem('offerRecentlyConsulted', JSON.stringify(this.offerIds));
  }
}

export const offerRecentlyConsulted = new OfferRecentlyConsulted();
