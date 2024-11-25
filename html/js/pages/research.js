// ---------------------------------------------------------------------------------------------- //
// Fetch data
// ---------------------------------------------------------------------------------------------- //

async function getOffers(limit = 5, offset = 0, order = 0, filters = []) {
    const host = window.location.protocol;
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
        searchParams.set("minimumEventDate", filters["minimumEventDate"] || null);
    }
    if (filters["maximumEventDate"]) {
        searchParams.set("maximumEventDate", filters["maximumEventDate"] || null);
    }
    if (filters["location"]) {
        searchParams.set("location", filters["location"] || null);
    }
    let search = searchParams.toString();
    console.log(search);
    let moreSearch = "";
    if (search) {
        moreSearch = "&";
    }
    const url = host + "/api/offers" + "?limit=" + limit + "&offset=" + offset + "&order=" + order + moreSearch + search;//url of the api for research page offers's data
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

console.time("getOffers");
let Data = await getOffers();
console.timeEnd("getOffers");

if (Data && !Array.isArray(Data)) {
    Data = Object.values(Data);
}
// console.log(Data);

// ---------------------------------------------------------------------------------------------- //
// Pop up
// ---------------------------------------------------------------------------------------------- //

let popup = document.getElementById("popup");
let filterButton = document.getElementById("filterButton");
const popupContent = document.querySelector(".popup-content");

filterButton.addEventListener("click", () => { popup.classList.toggle("hidden"); });

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
    })
})

