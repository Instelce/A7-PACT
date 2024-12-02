
let accordions = document.querySelectorAll(".accordion");

for (const accordion of accordions) {
    let trigger = accordion.querySelector(".accordion-trigger");
    let content = accordion.querySelector(".accordion-content");
    let chevron = trigger.querySelector("svg");

    trigger.addEventListener("click", () => {
        content.classList.toggle("hidden");
        if (content.classList.contains("hidden")) {
            chevron.style.transform = "rotate(0deg)";
        } else {
            chevron.style.transform = "rotate(180deg)";
        }
    })
}