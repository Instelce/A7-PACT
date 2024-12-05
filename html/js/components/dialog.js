let dialogTriggers = document.querySelectorAll(".dialog-trigger");
let dialogContainers = document.querySelectorAll('.dialog-container');
let closeClass = "close";

dialogTriggers.forEach(dialogTrigger => {
    let dialogName = dialogTrigger.getAttribute("data-dialog-trigger");
    let dialogContainer = Array.from(dialogContainers).filter(e => e.getAttribute('data-dialog-name') === dialogName)[0];

    dialogTrigger.addEventListener("click", () => {
        dialogContainer.classList.remove(closeClass);
    })
})

dialogContainers.forEach(dialogContainer => {
    let dialog = dialogContainer.querySelector(".dialog");
    let closeButton = dialogContainer.querySelector(".dialog-close");

    // Add event listener to close button
    if (closeButton) {
        closeButton.addEventListener("click", () => {
            dialogContainer.classList.add(closeClass);
        })
    }

    // Insert cross icon into dialog
    let crossIcon = document.createElement("button");
    crossIcon.classList.add("dialog-cross");
    crossIcon.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>`
    dialog.appendChild(crossIcon);

    crossIcon.addEventListener("click", () => {
        dialogContainer.classList.add(closeClass);
    })

    // Add event listener to container to close dialog
    dialogContainer.addEventListener("click", (e) => {
        if (e.target === dialogContainer) {
            dialogContainer.classList.add(closeClass);
        }
    })
})
