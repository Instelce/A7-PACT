import "../components/Carousel.js"

console.log("Home page");

let searchBar = document.getElementById("searchBar");
let searchButton = document.getElementById("searchButton");

searchButton.addEventListener("click", () => {
    let searchValue = searchBar.value;
    goSearchPage(searchValue);
});
searchBar.addEventListener("keyup", (event) => {
    if (event.key === "Enter") {
        let searchValue = searchBar.value;
        goSearchPage(searchValue);
    }
});
function goSearchPage(text) {
    window.location.href = `/recherche?search=${text}`;
}


