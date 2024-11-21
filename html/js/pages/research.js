let popup = document.getElementById("popup");
let filterButton = document.getElementById("filterButton");

filterButton.addEventListener("click", () => { popup.classList.toggle("hidden"); });


// ---------------------------------------------------------------------------------------------- //
// Category filter
// ---------------------------------------------------------------------------------------------- //

let categories = document.querySelectorAll(".category-item");

categories.forEach((category) => {
    category.addEventListener("click", () => {
        category.classList.toggle("active");
    })
})