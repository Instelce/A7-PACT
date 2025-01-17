import { getUser } from "../../user.js";
import {
    clientInfoCommand, formatDate, getChangesCommand,
    loginCommand, messageCard, REFRESH_RATE,
    sendMessageCommand, setLastMessage,
    userInfoCommand
} from "../../tchatator.js";

let appEnv = document.querySelector('input.app-environment').value;
let domain = window.location.hostname;

let is_writing = false;
let in_conversation_with = null;
let recipient_is_writing = false;
let recipient_is_connected = false;
let recipient_present_in_conversation = false;
let socket;

let user;
let listContacts = [];
let recipient_user;

let messageWriter = document.getElementById('message-writer');
let sendButton = document.querySelector('.send-button');
let messagesContainer = document.getElementById("message-container");
let writingIndicator = document.querySelector('.writing-indicator');

getUser().then(u => {
    user = u;

    if (appEnv == 'dev') {
        socket = new WebSocket(`ws://${domain}:4242`);
    } else {
        socket = new WebSocket(`wss://${domain}:4242`);
    }

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
            messagesContainer.appendChild(messageCard(socket, data.message, user, recipient_user, true));
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
            setLastMessage(data.message.receiver_id, 'Vous : ' + data.message.content);
        }

        if (data.command === 'USER_INFO') {
            console.log(data);
            recipient_is_writing = data.is_writing ? data.is_writing === 'true' : false;
            recipient_is_connected = data.connected ? data.connected === 'true' : false;
            recipient_present_in_conversation = data.in_conversation_with ? data.in_conversation_with === 'true' : false;

            if (recipient_is_writing) {
                writingIndicator.classList.remove('!hidden');
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
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
                        // Check if the contact exists
                        let contact = listContacts.find(c => c.account_id === change.message.sender_id);

                        // Add the contact if it doesn't exist
                        if (!contact) {
                            fetch(`/api/users/${change.message.sender_id}`)
                                .then(response => response.json())
                                .then(user => {
                                    console.log(user)
                                    listContacts.push(user);

                                    let card = document.createElement('article');
                                    card.classList.add('conversation-card');
                                    card.classList.add('rounded');
                                    card.classList.add('dashboard');
                                    card.innerHTML = `
                                        <img src="${user.avatar_url}" alt="profile picture">
                                        <div class="">
                                            <h3>${user.name}</h3>
                                            <h6 class="last-message line-clamp-1">${change.message.content}</h6>
                                        </div>
                                    `;

                                    card.id = user.account_id;

                                    // Add the click event to switch to the conversation
                                    card.addEventListener('click', () => {
                                        in_conversation_with = user.account_id;
                                        recipient_user = user;

                                        console.log('In conversation with: ', in_conversation_with);
                                        showDiscussion(user.account_id);

                                        // Show message writer container
                                        document.getElementById('message-writer-container').classList.remove('hidden');
                                    })

                                    document.getElementById('contactesListe').innerHTML = "";
                                    document.getElementById('contactesListe').appendChild(card);
                                })
                        } else {
                            // If exist and not in the conversation set the last message
                            // if (in_conversation_with != change.message.sender_id) {
                            //     setLastMessage(change.message.sender_id, change.message.content);
                            // }
                        }

                        setLastMessage(change.message.sender_id, change.message.content);

                        if (in_conversation_with == change.message.sender_id) {
                            change.message.modified_date = null;
                            messagesContainer.appendChild(messageCard(socket, change.message, user, recipient_user, true));
                            // Scroll to the bottom
                            messagesContainer.scrollTop = messagesContainer.scrollHeight;
                        }
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
        socket.send(getChangesCommand(user.api_token));
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
        card.classList.add('rounded');
        card.classList.add('dashboard');
        card.innerHTML = `
            <img src="${contact.avatar_url}" alt="profile picture">
            <div class="">
                <h3>${contact.name}</h3>
                <h6 class="last-message line-clamp-1">${user.account_id == contact.last_message.sender_id ? "Vous : " : ""}${contact.last_message.content}</h6>
            </div>
    `;
        card.id = contact.account_id;

        // Switch to the conversation with the user
        card.addEventListener('click', () => {
            in_conversation_with = contact.account_id;
            recipient_user = contact;

            console.log('In conversation with: ', in_conversation_with);
            showDiscussion(contact.account_id);

            // Show message writer container
            document.getElementById('message-writer-container').classList.remove('hidden');
        })

        contactsList.appendChild(card);
        listContacts.push(contact);
    });

    let svgnotfound = document.createElement('div');
    svgnotfound.classList.add('w-48');
    svgnotfound.classList.add('h-48');
    svgnotfound.innerHTML = '<svg viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g id="🔍-Product-Icons" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"> <g id="ic_fluent_people_search_24_filled" fill="#212121" fill-rule="nonzero"> <path d="M11.9092353,13.9995832 L19.7530511,13.999921 C20.9956918,13.999921 22.0030511,15.0072804 22.0030511,16.249921 L22.0030511,17.1550008 C22.0030511,18.2486786 21.5255957,19.2878579 20.6957793,20.0002733 C19.1303315,21.344244 16.8899962,22.0010712 14,22.0010712 L13.8209523,21.9999374 C14.1231382,21.3914232 14.0491694,20.6437608 13.5994596,20.1034984 L13.4890106,19.9826619 L11.2590774,17.758722 C11.7394467,16.9316429 12,15.9850969 12,15 C12,14.6583572 11.96885,14.3239899 11.9092353,13.9995832 Z M6.5,10.5 C8.98528137,10.5 11,12.5147186 11,15 C11,16.093806 10.6097492,17.0964639 9.96088672,17.8763348 L12.782748,20.6906119 C13.0759905,20.9831554 13.0765571,21.4580288 12.7840136,21.7512713 C12.5180649,22.0178554 12.1014304,22.0425586 11.8075592,21.8250546 L11.7233542,21.7525368 L8.82025196,18.8564864 C8.14273609,19.2649895 7.34881286,19.5 6.5,19.5 C4.01471863,19.5 2,17.4852814 2,15 C2,12.5147186 4.01471863,10.5 6.5,10.5 Z M6.5,12 C4.84314575,12 3.5,13.3431458 3.5,15 C3.5,16.6568542 4.84314575,18 6.5,18 C8.15685425,18 9.5,16.6568542 9.5,15 C9.5,13.3431458 8.15685425,12 6.5,12 Z M14,2.0046246 C16.7614237,2.0046246 19,4.24320085 19,7.0046246 C19,9.76604835 16.7614237,12.0046246 14,12.0046246 C11.2385763,12.0046246 9,9.76604835 9,7.0046246 C9,4.24320085 11.2385763,2.0046246 14,2.0046246 Z" id="🎨-Color"> </path> </g> </g> </g></svg>';
    messagesContainer.appendChild(svgnotfound);
    if (listContacts.length == 0) {
        let text = document.createElement('p');
        text.innerText = "Auncune demande.";
        contactsList.appendChild(text);
    }
    else {
        let text = document.createElement('p');
        text.innerText = "Choississez un contact pour continuer une conversation.";
        messagesContainer.appendChild(text);
    }
}

loadContacts();

// Show discussion
async function showDiscussion(account_id) {
    let messages = await loadMessages(account_id);
    messagesContainer.innerHTML = ""; // Clear previous messages
    messages.forEach(message => {
        messagesContainer.appendChild(messageCard(socket, message, user, recipient_user, true));
    });

    // Scroll to the bottom
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}
