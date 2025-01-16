import { getUser } from "../../user.js";
import {
    clientInfoCommand,
    loginCommand, messageCard, REFRESH_RATE,
    sendMessageCommand,
    userInfoCommand
} from "../../tchatator.js";

let is_writing = false;
let in_conversation_with = null;
let recipient_is_writing = false;

let user;
let listeContactes = [];
let recipient_user_id;

let messageWriter = document.getElementById('message-writer');
let sendButton = document.querySelector('.send-button');
let messagesContainer = document.getElementById("message-container");

getUser().then(u => {
    user = u;

    let socket = new WebSocket('ws://localhost:4242');

    socket.addEventListener("open", () => {
        console.log('Connected to the server');

        // Send login request
        socket.send(loginCommand(user.api_token));
    })

    socket.addEventListener("message", (event) => {
        let data = JSON.parse(event.data);

        if (data.command === 'USER_INFO') {
            recipient_is_writing = data.is_writing;
        }
        console.log('Recipient is writing: ', recipient_is_writing);
    })

    setInterval(() => {
        // Send info to the server about us
        socket.send(clientInfoCommand(user.api_token, is_writing, in_conversation_with));

        // Get information about recipient
        if (in_conversation_with) {
            socket.send(userInfoCommand(user.api_token, in_conversation_with));
        }
    }, REFRESH_RATE)

    socket.addEventListener("close", () => {
        console.log('Disconnected from the server');
    })

    // Send info when typing in the message writer
    messageWriter.addEventListener('input', () => {
        is_writing = messageWriter.value.length > 0;
    })

    // Send message
    sendButton.addEventListener('click', () => {
        if (messageWriter.value !== '') {
            socket.send(sendMessageCommand(user.api_token, messageWriter.value, in_conversation_with));

            messagesContainer.appendChild(messageCard({
                content: messageWriter.value,
                sender_id: user.id,
                receiver_id: in_conversation_with
            }, user, recipient_user_id));

            messageWriter.value = '';
        }
    })
})


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

async function contacts() {
    let contactsList = document.getElementById("contactesListe");
    let contacts = await loadContact();
    contacts.forEach(contacte => {
        let card = document.createElement('article');
        card.classList.add('conversation-card');
        card.innerHTML = `
            <img src="${contacte.avatar_url}" alt="profile picture">
            <div class="">
                <h3>${contacte.name}</h3>
            </div>
    `;
        card.id = contacte.account_id;
        contactsList.appendChild(card);
        listeContactes.push(contacte);
    });
}
contacts();


//listener for the contactes
document.getElementById("contactesListe").addEventListener("click", function (event) {
    if (event.target.closest('article')) {
        let sender = document.getElementById("message-writer-container");
        sender.classList.remove("hidden");
        let contactElements = document.querySelectorAll("#contactesListe article");
        contactElements.forEach(contact => {
            contact.classList.remove("active");
        });
        let selectedContact = event.target.closest('article');
        selectedContact.classList.add("active");
        showDiscussion(selectedContact.id);
        in_conversation_with = selectedContact.id;
        recipient_user_id = selectedContact.id;
    }
});

//function to sort the messages
function sortMessages(messages) {
    messages.sort((a, b) => {
        return new Date(a.created_at) - new Date(b.created_at);
    });
    return messages;
}

//show discussion
async function showDiscussion(account_id) {
    let messages = await loadMessages(account_id);
    messagesContainer.innerHTML = ""; // Clear previous messages
    sortMessages(messages);
    console.log(listeContactes);
    let recipient_user = listeContactes.find(contact => contact.account_id == account_id);
    console.log(recipient_user_id);
    messages.forEach(message => {
        messagesContainer.appendChild(messageCard(message, user, recipient_user));
    });
}

