
let accordions = document.querySelectorAll(".accordion");

for (const accordion of accordions) {
    let trigger = accordion.querySelector(".accordion-trigger");
    let content = accordion.querySelector(".accordion-content");
    let chevron = trigger.querySelector("svg");

    trigger.addEventListener("click", () => {
        content.classList.toggle("hidden");
        if (content.classList.contains("hidden")) {
            chevron.style.transform = "rotate(0deg)";
        } else {
            chevron.style.transform = "rotate(180deg)";
        }
    })
}

let cards = document.querySelectorAll(".invoice-card");

for (const card of cards) {
    let toggleHistory = card.querySelector("#toggle-histories");
    let histories = card.querySelector(".card-histories");

    toggleHistory.addEventListener("click", () => {
        resetHistory(histories);
        histories.classList.toggle("hidden");

        if (histories.classList.contains("hidden")) {
            toggleHistory.innerHTML = "Voir l'historique";
        } else {
            toggleHistory.innerHTML = "Cacher l'historique";
        }
    })
}

function resetHistory(el) {
    let histories = document.querySelectorAll(".card-histories");
    for (const history of histories) {
        if (history !== el)
            history.classList.add("hidden");
    }
}