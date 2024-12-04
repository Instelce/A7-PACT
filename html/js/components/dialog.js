let dialogTriggers = document.querySelectorAll(".dialog-trigger");
let dialogContainers = document.querySelectorAll('.dialog-container')
console.log(dialogTriggers);

dialogTriggers.forEach(dialogTrigger => {
    let dialogName = dialogTrigger.getAttribute("data-dialog-trigger");
    let dialogContainer = Array.from(dialogContainers).filter(e => e.getAttribute('data-dialog-name') === dialogName)[0];
    dialogTrigger.addEventListener("click", () => {
        dialogContainer.classList.remove("hidden");
    })
})
