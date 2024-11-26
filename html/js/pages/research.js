// ---------------------------------------------------------------------------------------------- //
// Fetch data
// ---------------------------------------------------------------------------------------------- //

async function getOffers(filters = [], limit = 5, offset = 0, order = null) {
    const host = window.location.protocol;
    const searchParams = new URLSearchParams();
    if (order) {
        searchParams.set("order", order || null);
    }
    if (filters["category"]) {
        searchParams.set("category", filters["category"] || null);
    }
    if (filters["minimumOpinions"] && filters["minimumOpinions"] > 0) {
        searchParams.set("minimumOpinions", filters["minimumOpinions"] || null);
    }
    if (filters["maximimOpinions"] && filters["maximimOpinions"] > 0) {
        searchParams.set("maximimOpinions", filters["maximimOpinions"] || null);
    }
    if (filters["minimumPrice"] && filters["minimumPrice"] > 0) {
        searchParams.set("minimumPrice", filters["minimumPrice"] || null);
    }
    if (filters["maximumPrice"] && filters["maximumPrice"] > 0) {
        searchParams.set("maximumPrice", filters["maximumPrice"] || null);
    }
    if (filters["open"]) {
        searchParams.set("open", filters["open"] || null);
    }
    if (filters["minimumEventDate"]) {
        searchParams.set(
            "minimumEventDate",
            filters["minimumEventDate"] || null
        );
    }
    if (filters["maximumEventDate"]) {
        searchParams.set(
            "maximumEventDate",
            filters["maximumEventDate"] || null
        );
    }
    if (filters["location"]) {
        searchParams.set("location", filters["location"] || null);
    }
    let search = searchParams.toString();
    let moreSearch = "";
    if (search) {
        moreSearch = "&";
    }
    const url =
        host +
        "/api/offers" +
        "?limit=" +
        limit +
        "&offset=" +
        offset +
        moreSearch +
        search; //url of the api for research page offers's data
    console.log("url : " + url);
    try {
        const response = await fetch(url); //fetching the data from the api
        if (!response.ok) {
            throw new Error(`Response status: ${response.status}`); //if the response is not ok, throw an error
        }

        const json = await response.json(); //converting the response to json
        return json; //return the json data if all is successful
    } catch (error) {
        console.error(error.message); //logging the error message
        return false; //returning false if an error occurs
    }
}
// ---------------------------------------------------------------------------------------------- //
// Display logic
// ---------------------------------------------------------------------------------------------- //
// let filters = { category: "visit" };
console.time("getOffers");
let Data = await getOffers();
console.timeEnd("getOffers");

displayOffers(Data);

//testing
if (Data && !Array.isArray(Data)) {
    Data = Object.values(Data);
}
console.log(Data);

async function applyFilters(newFilters) {
    const currentFilters = {
        category: document.querySelector(".category-item.active")?.dataset.category || null,
        minimumOpinions: document.getElementById("minimumOpinions")?.value || null,
        maximumOpinions: document.getElementById("maximumOpinions")?.value || null,
        minimumPrice: document.getElementById("minimumPrice")?.value || null,
        maximumPrice: document.getElementById("maximumPrice")?.value || null,
        open: document.getElementById("open")?.checked || null,
        minimumEventDate: document.getElementById("minimumEventDate")?.value || null,
        maximumEventDate: document.getElementById("maximumEventDate")?.value || null,
        location: document.getElementById("location")?.value || null,
    };
    const filters = { ...currentFilters, ...newFilters };
    Object.keys(filters).forEach(key => {
        if (filters[key] === null || filters[key] === "" || filters[key] === false || filters[key] === undefined || filters[key] === 0) {
            delete filters[key];
        }
    });
    let Data = await getOffers(filters);
    displayOffers(Data);
}
// ---------------------------------------------------------------------------------------------- //
// Display
// ---------------------------------------------------------------------------------------------- //

function displayOffers(Data) {
    const offersContainer = document.querySelector(".flex.flex-col.gap-2");
    if (!offersContainer) {
        console.error("Offers container not found");
    } else if (!Array.isArray(Data)) {
        console.error("Data is not an array");
    } else {
        offersContainer.innerHTML = "";
        Data.forEach((offer) => {
            const offerElement = document.createElement("a");
            offerElement.href = `/offres/${offer.id}`;
            offerElement.innerHTML = `
            <article class="research-card">
                <div class="research-card--photo">
                    ${offer.photos[0]
                    ? `<img alt="photo d'article" src="${offer.photos[0]}" />`
                    : ""
                }
                </div>
                <div class="research-card--body">
                    <header>
                        <h2 class="research-card--title">${offer.title}</h2>
                        <p>${translateCategory(
                    offer.category
                )} par <a href="/comptes/${offer.professional_id
                }" class="underline">${offer.profesionalUser["denomination"]}</a></p>
                    </header>
                    <p class="summary">${offer.summary}</p>
                         <div class="flex gap-2 mt-auto pt-4">
                            <a href="" class="button gray w-full spaced">Itinéraire<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map"><path d="M14.106 5.553a2 2 0 0 0 1.788 0l3.659-1.83A1 1 0 0 1 21 4.619v12.764a1 1 0 0 1-.553.894l-4.553 2.277a2 2 0 0 1-1.788 0l-4.212-2.106a2 2 0 0 0-1.788 0l-3.659 1.83A1 1 0 0 1 3 19.381V6.618a1 1 0 0 1 .553-.894l4.553-2.277a2 2 0 0 1 1.788 0z"/><path d="M15 5.764v15"/><path d="M9 3.236v15"/></svg></a>
                            <a href="" class="button blue w-full spaced">Voir plus<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right"><path d="m9 18 6-6-6-6"/></svg></a>
                        </div>
                </div>
            </article>
        `;
            offersContainer.appendChild(offerElement);
        });
    }
}

function translateCategory(category) {
    switch (category) {
        case "attraction_park":
            return "parc d'attraction";
        case "visit":
            return "visite";
        case "restaurant":
            return "restaurant";
        case "activity":
            return "activité";
        case "show":
            return "spectacle";
    }
}
// ---------------------------------------------------------------------------------------------- //
// Pop up
// ---------------------------------------------------------------------------------------------- //

let popup = document.getElementById("popup");
let filterButton = document.getElementById("filterButton");
const popupContent = document.querySelector(".popup-content");

filterButton.addEventListener("click", () => {
    popup.classList.toggle("hidden");
});

popup.addEventListener("click", (event) => {
    if (!popupContent.contains(event.target)) {
        popup.classList.add("hidden");
    }
});

// ---------------------------------------------------------------------------------------------- //
// Category filter
// ---------------------------------------------------------------------------------------------- //

let categoryListenners = [
    "spectacles",
    "restauration",
    "visites",
    "activités",
    "attractions",
];
let categoryValue = [
    "show",
    "restaurant",
    "visit",
    "activity",
    "attraction_park",
];

categoryListenners.forEach((listener, index) => {
    const element = document.getElementById(listener);
    let categories = document.querySelectorAll(".category-item");
    if (element) {
        element.addEventListener("click", () => {
            if (element.classList.contains("active")) {
                element.classList.remove("active");
                applyFilters();
            } else {
                categories.forEach((cat) => cat.classList.remove("active"));
                element.classList.add("active");
                applyFilters({ category: categoryValue[index] });
            }
        });
    } else {
        console.warn(`Element with ID ${listener} not found`);
    }
});


// ---------------------------------------------------------------------------------------------- //
// listeners
// ---------------------------------------------------------------------------------------------- //


//testing
setInterval(() => {
    applyFilters({});
    console.log("Filters applied");
}, 10000);