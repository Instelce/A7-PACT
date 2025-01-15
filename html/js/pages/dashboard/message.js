import { getUser } from "../../user.js";
//get the actual user
let user = await getUser();
//function to load the messages
async function loadMessages(account_id) {
    try {
        const response = await fetch("/api/messages/" + account_id); //fetching the data from the api
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

async function loadContact() {
    try {
        const response = await fetch("/api/messages"); //fetching the data from the api
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

//create contacte tabs

async function contactes() {
    let contactesListe = document.getElementById("contactesListe");
    let contactes = await loadContact();
    contactes.forEach(contacte => {
        let div = document.createElement("div");
        div.id = contacte.account_id;
        div.textContent = contacte.name;
        contactesListe.appendChild(div);
    });

}

contactes();

//listener for the contactes
document.getElementById("contactesListe").addEventListener("click", function (event) {
    if (event.target && event.target.nodeName === "DIV") {
        showDiscussion(event.target.id);
    }
});

//show discussion

async function showDiscussion(account_id) {
    let messages = await loadMessages(account_id);
    let discussion = document.getElementById("message-container");
    discussion.innerHTML = ""; // Clear previous messages
    messages.forEach(message => {
        let messageDiv = document.createElement("div");
        messageDiv.className = "message";
        messageDiv.textContent = message.content;
        discussion.appendChild(messageDiv);
    });
}

