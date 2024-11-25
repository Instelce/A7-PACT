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