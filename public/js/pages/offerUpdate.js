import '../address.js';

// -------------------------------------------------------------------------- //
// Set sidebar top position
// -------------------------------------------------------------------------- //

let sidebar = document.querySelector('#sidebar');
let navbar = document.querySelector('.navbar');

sidebar.style.top = `${navbar.offsetHeight}px`;


// -------------------------------------------------------------------------- //
// Price grid new row
// -------------------------------------------------------------------------- //

let priceTableBody = priceTable.querySelector('tbody#prices-rows');
let priceAddButton = document.querySelector('#add-price-row');

priceAddButton.addEventListener('click', (e) => {
    e.preventDefault();
    priceTableBody.insertAdjacentHTML('beforeend', `
        <tr>
            <td>
                <input id="price-name" name="prices[]" type="text" placeholder="Nom" class="table-input">
            </td>
            <td>
                <input id="price-value" name="prices[]" type="number" placeholder="Prix" class="table-input">
            </td>
        </tr>
    `);
})


// -------------------------------------------------------------------------- //
// Photo upload
// -------------------------------------------------------------------------- //

let photoInput = document.querySelector('#photo-input');
let photosContainer = document.querySelector('#photos');
let imageUploaderContainer = document.querySelector('.image-uploader');

imageUploaderContainer.addEventListener('dragover', (e) => {
    e.preventDefault();
    imageUploaderContainer.classList.add("over");
})

imageUploaderContainer.addEventListener("drop", (e) => {
    e.preventDefault();
    photoInput.files = e.dataTransfer.files;
    photoInput.dispatchEvent(new Event('change'));
    imageUploaderContainer.classList.remove("over");
})

photoInput.addEventListener('change', (e) => {
    let files = e.target.files;

    for (let i = 0; i < files.length; i++) {
        let file = files[i];

        let reader = new FileReader();
        reader.onload = function (e) {
            let photoCard = document.createElement('div');
            photoCard.classList.add('uploaded-image-card');
            photoCard.setAttribute('draggable', 'true');
            photoCard.innerHTML = `
                <input type="file" name="photos[]" accept="image/*" multiple="true" hidden>
                <div class="image-container">        
                    <img src="${e.target.result}" alt="Photo" class="w-20 h-20 object-cover rounded-lg" draggable="false">
                </div>
                <div class="card-buttons">
                    <div>
                        <button class="photo-remove button gray no-border only-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash-2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                        </button>
                        <button class="photo-maximise button gray no-border only-icon" disabled>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-maximize-2"><polyline points="15 3 21 3 21 9"/><polyline points="9 21 3 21 3 15"/><line x1="21" x2="14" y1="3" y2="10"/><line x1="3" x2="10" y1="21" y2="14"/></svg>                
                        </button>
                        <button class="photo-crop button gray no-border only-icon" disabled>
                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-crop"><path d="M6 2v14a2 2 0 0 0 2 2h14"/><path d="M18 22V8a2 2 0 0 0-2-2H2"/></svg>             
                        </button>
                    </div>
                   
                    <button class="photo-drag button gray no-border only-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-grip-vertical"><circle cx="9" cy="12" r="1"/><circle cx="9" cy="5" r="1"/><circle cx="9" cy="19" r="1"/><circle cx="15" cy="12" r="1"/><circle cx="15" cy="5" r="1"/><circle cx="15" cy="19" r="1"/></svg>
                    </button>
                </div>
            `;

            // Set the file input value
            let input = photoCard.querySelector('input[type="file"]');
            let dataTransfer = new DataTransfer();
            let fileCopy = new File([file.slice(0, file.size, file.type)], file.name);
            dataTransfer.items.add(fileCopy);
            input.files = dataTransfer.files;

            photosContainer.insertBefore(photoCard, photosContainer.firstChild);

            // -------------------------------------------------------------------------- //
            // Photo drag order
            // -------------------------------------------------------------------------- //

            const images = document.querySelectorAll('#photos .uploaded-image-card');

            // Remove event listeners
            images.forEach(image => {
                image.removeEventListener('dragstart', dragStart);
                image.removeEventListener('dragend', dragEnd);
            })

            // Add event listeners
            images.forEach(image => {
                image.addEventListener('dragstart', dragStart);
                image.addEventListener('dragend', dragEnd);
            })

            // -------------------------------------------------------------------------- //
            // Remove photo
            // -------------------------------------------------------------------------- //

            let removeButtons = document.querySelectorAll('.photo-remove');
            removeButtons.forEach(button => {
                button.addEventListener('click', (e) => {
                    e.target.closest('.uploaded-image-card').remove();
                })
            })

            // -------------------------------------------------------------------------- //
            // Maximise photo
            // -------------------------------------------------------------------------- //
        }

        reader.readAsDataURL(files[i]);
    }
})

function dragStart(e) {
    e.target.classList.add('dragging');
}

function dragEnd(e) {
    e.target.classList.remove('dragging');
}


// -------------------------------------------------------------------------- //
// Photo drag and drop to reorder
// -------------------------------------------------------------------------- //

let dragZone = document.querySelector('#photos');
let dragLine = document.querySelector('.drag-line');

dragZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    const afterElement = getDragAfterElement(dragZone, e.clientY);
    const draggingElement = document.querySelector('.dragging');
    // dragLine.classList.remove("hidden");

    if (afterElement == null) {
        dragZone.appendChild(draggingElement);
    } else {
        dragZone.insertBefore(dragLine, afterElement);
    }
})

function getDragAfterElement(container, y) {
    const draggableElements = [...container.querySelectorAll('.uploaded-image-card:not(.dragging)')];

    return draggableElements.reduce((closest, child) => {
        const box = child.getBoundingClientRect();
        const offset = y - box.top - box.height / 2;
        if (offset < 0 && offset > closest.offset) {
            return { offset: offset, element: child };
        } else {
            return closest;
        }
    }, { offset: Number.NEGATIVE_INFINITY }).element;
}

dragZone.addEventListener("drageenter", (e) => {
    e.preventDefault();
    dragZone.classList.add("over");
})

dragZone.addEventListener("dragleave", (e) => {
    e.preventDefault();
    dragZone.classList.remove("over");
})

dragZone.addEventListener("drop", (e) => {
    e.preventDefault();
    dragLine.classList.add("hidden");

    const afterElement = getDragAfterElement(dragZone, e.clientY);
    const draggable = document.querySelector('.dragging');

    if (afterElement == null) {
        dragZone.appendChild(draggable);
    } else {
        dragZone.insertBefore(draggable, afterElement);
    }

    draggable.classList.remove("hidden");
})


// -------------------------------------------------------------------------- //
// Calculate prices
// -------------------------------------------------------------------------- //

let typeStandardInput = document.querySelector('#type-standard');
let typePremiumInput = document.querySelector('#type-premium');

let priceWithoutOption = document.querySelector('#price-without-option');
let priceWithOption = document.querySelector('#price-with-option');
let priceSubTotal = document.querySelector('#price-subtotal');

let offerPrices = {
    "standard": 4.98,
    "premium": 7.98,
}

let optionPrices = {
    "no": 0,
    "en-relief": 2.98,
    "a-la-une": 4.98,
}

if (typeStandardInput && typePremiumInput) {
    typeStandardInput.addEventListener('input', updatePrices);
    typePremiumInput.addEventListener('input', updatePrices);
    noOption.addEventListener('input', updatePrices);
    enreliefOption.addEventListener('input', updatePrices);
    alauneOption.addEventListener('input', updatePrices);
}

function updatePrices() {
    let type = typeStandardInput.checked ? "standard" : "premium";
    let option = noOption.checked ? "no" : enreliefOption.checked ? "en-relief" : "a-la-une";

    priceWithoutOption.textContent = `${offerPrices[type]} €`;
    priceWithOption.textContent = `${(offerPrices[type] + optionPrices[option]).toFixed(2)} €`;
    priceSubTotal.textContent = `${(offerPrices[type] + optionPrices[option]).toFixed(2)} €`;
}