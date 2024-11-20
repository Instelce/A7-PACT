let isAsso = document.getElementById("asso");
let divAsso = document.querySelector(".siren");

if (isAsso && divAsso) {
    isAsso.addEventListener('change', () => {
        if (divAsso.classList.contains('hidden')) {
            divAsso.classList.remove('hidden');
            divAsso.classList.add('block');
        } else {
            divAsso.classList.remove('block');
            divAsso.classList.add('hidden');
        }
    });
}