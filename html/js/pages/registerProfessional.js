const isAsso = document.getElementById("asso");
const divAsso = document.getElementById("siren");

if (isAsso && divAsso) {
    isAsso.addEventListener('change', () => {
        if (isAsso.checked) {
            divAsso.classList.remove('hidden');
        } else {
            divAsso.classList.add('hidden');
        }
    });
}

document.addEventListener("DOMContentLoaded", () => {
    const conditionsPublic = document.getElementById("switch-cond-public");
    const conditionsPrivate = document.getElementById("switch-cond-private");
    const buttonPublic = document.getElementById("submitFormProPublic");
    const buttonPrivate = document.getElementById("submitFormProPrivate");

    const ButtonStatePublic = () => {
        if (conditionsPublic.checked) {
            buttonPublic.disabled = false;
            buttonPublic.classList.remove("opacity-50", "cursor-not-allowed");
        } else {
            buttonPublic.disabled = true;
            buttonPublic.classList.add("opacity-50", "cursor-not-allowed");
        }
    };

    const ButtonStatePrivate = () => {
        if (conditionsPrivate.checked) {
            buttonPrivate.disabled = false;
            buttonPrivate.classList.remove("opacity-50", "cursor-not-allowed");
        } else {
            buttonPrivate.disabled = true;
            buttonPrivate.classList.add("opacity-50", "cursor-not-allowed");
        }
    };

    conditionsPublic.addEventListener("change", ButtonStatePublic);
    ButtonStatePublic();

    conditionsPrivate.addEventListener("change", ButtonStatePrivate);
    ButtonStatePrivate();
});