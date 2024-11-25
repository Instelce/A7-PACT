
let cardOption = document.querySelector('.card-option');
let addOptionButton  = document.getElementById('add-option-button');
let addOptionForm = document.getElementById('add-option-form');
let closeOptionForm = document.getElementById('close-option-form');

addOptionButton.addEventListener('click', function() {
    addOptionForm.classList.remove('hidden');
    cardOption.classList.add('!hidden');
});

closeOptionForm.addEventListener('click', function() {
    addOptionForm.classList.add('hidden');
    cardOption.classList.remove('!hidden');
})