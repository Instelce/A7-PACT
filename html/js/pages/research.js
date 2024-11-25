// ---------------------------------------------------------------------------------------------- //
// Fetch data
// ---------------------------------------------------------------------------------------------- //

async function getOffers(limit = 5, offset = 0, order = 0, filters = []) {
    const host = window.location.protocol + "//" + window.location.host;
    const searchParams = new URLSearchParams();
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
        "&order=" +
        order +
        moreSearch +
        search; //url of the api for research page offers's data
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

console.time("getOffers");
let Data = await getOffers();
console.timeEnd("getOffers");

if (Data && !Array.isArray(Data)) {
    Data = Object.values(Data);
}
console.log(Data);
console.log();
const offersContainer = document.querySelector(".flex.flex-col.gap-2");
if (!offersContainer) {
    console.error("Offers container not found");
} else if (!Array.isArray(Data)) {
    console.error("Data is not an array");
} else {
    Data.forEach((offer) => {
        const offerElement = document.createElement("a");
        offerElement.href = `/offres/${offer.id}`;
        offerElement.innerHTML = `
            <article class="research-card">
                <div class="research-card--photo">
                    ${
                        offer.photos[0]
                            ? `<img alt="photo d'article" src="${offer.photos[0]}" />`
                            : ""
                    }
                </div>
                <div class="research-card--body">
                    <header>
                        <h2 class="research-card--title">${offer.title}</h2>
                        <p>${
                            offer.type
                        } par <a href="/comptes/${offer}" class="underline">${offer}</a></p>
                    </header>
                    <p class="summary">${offer.summary}</p>
                    <div class="flex gap-2 mt-auto pt-4">
                        <a href="" class="button gray w-full spaced">Itinéraire<i data-lucide="map"></i></a>
                        <a href="" class="button blue w-full spaced">Voir plus<i data-lucide="chevron-right"></i></a>
                    </div>
                </div>
            </article>
        `;
        offersContainer.appendChild(offerElement);
    });
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

let categories = document.querySelectorAll(".category-item");

categories.forEach((category) => {
    category.addEventListener("click", () => {
        category.classList.toggle("active");
    });
});
