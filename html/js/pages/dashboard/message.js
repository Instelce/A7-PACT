import { getUser } from "../../user.js";
import {
    clientInfoCommand, formatDate, getChangesCommand,
    loginCommand, messageCard, REFRESH_RATE,
    sendMessageCommand,
    userInfoCommand
} from "../../tchatator.js";

let is_writing = false;
let in_conversation_with = null;
let recipient_is_writing = false;
let recipient_is_connected = false;
let recipient_present_in_conversation = false;
let socket;

let user;
let listeContactes = [];
let recipient_user;

let messageWriter = document.getElementById('message-writer');
let sendButton = document.querySelector('.send-button');
let messagesContainer = document.getElementById("message-container");
let writingIndicator = document.querySelector('.writing-indicator');

getUser().then(u => {
    user = u;

    socket = new WebSocket('ws://localhost:4242');

    socket.addEventListener("open", () => {
        console.log('Connected to the server');

        // Send login request
        socket.send(loginCommand(user.api_token));
    })

    socket.addEventListener("message", (event) => {
        console.log('')

        let data = JSON.parse(event.data);

        if (data.command === 'SEND_MSG') {
            data.message.modified_date = null;
            messagesContainer.appendChild(messageCard(socket, data.message, user, recipient_user));
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        if (data.command === 'USER_INFO') {
            console.log(data);
            recipient_is_writing = data.is_writing ? data.is_writing === 'true' : false;
            recipient_is_connected = data.connected ? data.connected === 'true' : false;
            recipient_present_in_conversation = data.in_conversation_with ? data.in_conversation_with === 'true' : false;

            if (recipient_is_writing) {
                writingIndicator.classList.remove('!hidden');
            } else {
                writingIndicator.classList.add('!hidden');
            }
        }

        // New message change available
        if (data.command === 'NEW_CHG_AVAILABLE') {
            console.log(data)

            if (data.changes.length > 0) {
                for (let change of data.changes) {
                    if (change.type === 'new_message') {
                        console.log("NEW MSG", user, recipient_user);
                        change.message.modified_date = null;
                        messagesContainer.appendChild(messageCard(socket, change.message, user, recipient_user));
                        // Scroll to the bottom
                        messagesContainer.scrollTop = messagesContainer.scrollHeight;
                    }

                    if (change.type === 'message_updated') {
                        let messageCard = messagesContainer.querySelector(`[data-id="${change.message.id}"]`);
                        let messageContent = messageCard.querySelector('.message-content pre');
                        messageContent.innerText = change.message.content;

                        // Remove modified text is exists and add it again
                        let modifiedText = messageCard.querySelector('.modified');
                        if (modifiedText) {
                            messageCard.querySelector('.message-content').removeChild(modifiedText);
                        }
                        let newModifiedText = document.createElement('small');
                        newModifiedText.classList.add('modified');
                        newModifiedText.innerText = 'Modifié il y a' + formatDate(change.message.modified_date);
                        messageCard.querySelector('.message-content').appendChild(newModifiedText);
                    }

                    if (change.type === 'message_deleted') {
                        let messageCard = messagesContainer.querySelector(`[data-id="${change.message_id}"]`);
                        let messageContent = messageCard.querySelector('.message-content pre');
                        messageContent.innerHTML = '<p>Ce message à été supprimé</p>';
                        messageCard.classList.add('deleted');

                        // Remove modified text is exists
                        let modifiedText = messageCard.querySelector('.modified');
                        if (modifiedText) {
                            messageCard.querySelector('.message-content').removeChild(modifiedText);
                        }
                    }
                }
            }
        }

        console.log('Is connected', data.connected);
        console.log('Recipient is writing:', recipient_is_writing);
        console.log('Recipient is present in conversation: ', recipient_present_in_conversation);
    })

    setInterval(() => {
        socket.send(clientInfoCommand(user.api_token, is_writing, in_conversation_with));
        if (in_conversation_with) {
            socket.send(userInfoCommand(user.api_token, in_conversation_with));
            socket.send(getChangesCommand(user.api_token));
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

            messageWriter.value = '';
            is_writing = false;
        }
    })
})


// Function to load the messages
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

// Create contact list
async function loadContacts() {
    let contactsList = document.getElementById("contactesListe");
    let contacts = await loadContact();

    contacts.forEach(contact => {
        let card = document.createElement('article');
        card.classList.add('conversation-card');
        card.innerHTML = `
            <img src="${contact.avatar_url}" alt="profile picture">
            <div class="">
                <h3>${contact.name}</h3>
            </div>
    `;
        card.id = contact.account_id;

        // Switch to the conversation with the user
        card.addEventListener('click', () => {
            in_conversation_with = contact.account_id;
            console.log(contact)
            recipient_user = contact;

            console.log('In conversation with: ', in_conversation_with);
            showDiscussion(contact.account_id);

            // Show message writer container
            document.getElementById('message-writer-container').classList.remove('hidden');
        })

        contactsList.appendChild(card);
        listeContactes.push(contact);
    });
}

loadContacts();

// Show discussion
async function showDiscussion(account_id) {
    let messages = await loadMessages(account_id);
    messagesContainer.innerHTML = ""; // Clear previous messages
    console.log(listeContactes);
    let recipient_user = listeContactes.find(contact => contact.account_id == account_id);
    console.log(recipient_user_id);
    messages.forEach(message => {
        messagesContainer.appendChild(messageCard(socket, message, user, recipient_user));
    });

    // Scroll to the bottom
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}
