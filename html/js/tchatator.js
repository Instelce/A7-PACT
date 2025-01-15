import {getUser} from "./user.js";

// ---------------------------------------------------------------------------------------------- //
// Tchatator socket client that connects to the C server
// Host: localhost
// Port: 4242
// ---------------------------------------------------------------------------------------------- //

// Member client

export const REFRESH_RATE = 500;

let is_writing = false;
let in_conversation_with = null;
let recipient_is_writing = false;
let recipient_is_connected = false;
let recipient_present_in_conversation = false;
let user = null;
let recipient_user = null;
let socket;


// All elements
let chat = document.querySelector('.chat-container');
let conversationsPage;
let messagesPage;
let conversationsContainer;
let chatTrigger;
let messagesContainer;
let messageWriter;
let sendButton;
let writingIndicator;

if (chat) {
    conversationsPage = chat.querySelector('.conversations-page');
    messagesPage = chat.querySelector('.messages-page');
    conversationsContainer = chat.querySelector('.conversations-gen');
    chatTrigger = document.querySelector('.chat-trigger');
    messagesContainer = chat.querySelector('.messages-container');
    messageWriter = chat.querySelector('#message-writer');
    sendButton = chat.querySelector('.send-button');
    writingIndicator = chat.querySelector('.writing-indicator');
}


getUser().then(_user => {
    user = _user;

    if (user && user.type !== 'professional') {
        socket = new WebSocket('ws://localhost:4242');

        socket.addEventListener("open", () => {
            console.log('Connected to the server');

            // Send login request
            socket.send(loginCommand(user.api_token));
        });

        socket.addEventListener("message", (event) => {
            let data = JSON.parse(event.data);

            if (data.command === 'USER_INFO') {
                console.log(data);
                recipient_is_writing = data.is_writing ? data.is_writing === 'true' : false;
                recipient_is_connected = data.connected ? data.connected === 'true' : false;
                // recipient_present_in_conversation = data.in_conversation_with === user.id;

                if (recipient_is_writing) {
                    writingIndicator.classList.remove('!hidden');
                } else {
                    writingIndicator.classList.add('!hidden');
                }
            }

            console.log('Is connected', data.connected);
            console.log('Recipient is writing:', recipient_is_writing);
            // console.log(data.in_conversation_with)
            // console.log('Recipient is present in conversation: ', recipient_present_in_conversation);
        });

        socket.addEventListener("close", () => {
            console.log('Disconnected from the server');
        });

        socket.addEventListener("error", (error) => {
            console.log('Error: ', error);
        });

        // Send info to the server about us
        setInterval(() => {
            socket.send(clientInfoCommand(user.api_token, is_writing, in_conversation_with));
            if (in_conversation_with) {
                socket.send(userInfoCommand(user.api_token, in_conversation_with));
            }
        }, REFRESH_RATE)

        // Toggle chat
        chatTrigger.addEventListener('click', () => {
            chat.classList.toggle('hidden');
        })

        // Send message
        sendButton.addEventListener('click', () => {
            if (messageWriter.value !== '') {
                socket.send(sendMessageCommand(user.api_token, messageWriter.value, in_conversation_with));

                messagesContainer.appendChild(messageCard({
                    content: messageWriter.value,
                    sender_id: user.account_id,
                    receiver_id: in_conversation_with
                }));

                messageWriter.value = '';
            }
        })

        // Send info when typing in the message writer
        messageWriter.addEventListener('input', () => {
            is_writing = messageWriter.value !== '';
        })

        // Load conversations at the beginning
        loadConversations(conversationsContainer);
    }
})

function loadConversations() {
    fetch('/api/messages')
        .then(r => r.json())
        .then(users => {
            users.forEach(user => {
                conversationsContainer.appendChild(conversationCard(user));
            })
        })
}

function loadMessages(receiverId) {
    messagesContainer.innerHTML = '';

    fetch(`/api/messages/${receiverId}`)
        .then(r => r.json())
        .then(messages => {
            messages.forEach(message => {
                messagesContainer.appendChild(messageCard(message, user, recipient_user));
            })
        })
}

export function messageCard(message, user, recipient_user) {
    let sent = message.sender_id === user.account_id;

    let card = document.createElement('div');

    // Add classes
    card.classList.add('message');
    card.classList.add(sent ? 'sent' : 'received');
    if (message.deleted) {
        card.classList.add('deleted');
    }

    // Modify date format
    let dateText = '';
    if (message.modified_date) {
        let diff = new Date() - new Date(message.modified_date);

        let months = Math.floor(diff / (1000 * 60 * 60 * 24 * 30));
        let weeks = Math.floor(diff / (1000 * 60 * 60 * 24 * 7));
        let days = Math.floor(diff / (1000 * 60 * 60 * 24));
        let hours = Math.floor(diff / (1000 * 60 * 60));
        let minutes = Math.floor(diff / (1000 * 60));
        let seconds = Math.floor(diff / 1000);

        if (months > 0) {
            dateText = `${months}mo`;
        } else if (weeks > 0) {
            dateText = `${weeks}sem`;
        } else if (days > 0) {
            dateText = `${days}j`;
        } else if (hours > 0) {
            dateText = `${hours}h`;
        } else if (minutes > 0) {
            dateText = `${minutes}min`;
        } else {
            dateText = `${seconds}s`;
        }
    }

    card.innerHTML = `
        <div class="message-content">
            ${!sent ? `<small>${recipient_user.name}</small>` : '<small>Vous</small>'}
            ${message.deleted ? `<p>Ce message à été supprimé</p>` : `<pre>${message.content}</pre>`}
            ${message.modified_date && !message.deleted ? `<small>Modifié il y a ${dateText}</small>` : ''}
        </div>
        ${sent ? `<div class="buttons">
            <button class="delete-message">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg> 
            </button>
            <button class="update-message">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pencil"><path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/><path d="m15 5 4 4"/></svg>
            </button>
        </div>` : ''}
    `;

    if (sent) {
        let buttons = card.querySelector('.buttons');
        let messageContent = card.querySelector('.message-content pre');

        // Delete message
        let deleteButton = card.querySelector('.delete-message');
        deleteButton.addEventListener('click', () => {
            card.classList.add('deleted');
            messageContent.innerHTML = '<p>Ce message à été supprimé</p>';
            socket.send(deleteMessageCommand(user.api_token, message.id));
        })

        // Toggle update form
        let toggleUpdateForm = card.querySelector('.update-message');

        toggleUpdateForm.addEventListener('click', () => {
            // Remove message content and hide buttons
            messageContent.innerHTML = '';
            buttons.classList.add('!hidden');

            // Add input and update button
            let updateInput = document.createElement('input');
            updateInput.value = message.content;
            updateInput.classList.add('update-input');

            let updateButton = document.createElement('button');
            updateButton.textContent = 'Update';
            updateButton.classList.add('update-button');

            updateButton.addEventListener('click', () => {
                messageContent.innerHTML = updateInput.value;
                socket.send(updateMessageCommand(user.api_token, message.id, updateInput.value));

                // Remove input and button
                card.querySelector('.message-content').removeChild(updateInput);
                card.querySelector('.message-content').removeChild(updateButton);
                buttons.classList.remove('!hidden');
            })

            card.querySelector('.message-content').appendChild(updateInput);
            card.querySelector('.message-content').appendChild(updateButton);
        })
    }

    return card;
}

function conversationCard(_user) {
    let card = document.createElement('article');
    card.classList.add('conversation-card');
    card.innerHTML = `
            <a href="${_user.account_id}">
              <img src="${_user.avatar_url}" alt="profile picture">
            </a>
            <div class="">
                <h3>${_user.name}</h3>
            </div>
    `;

    // Switch to the conversation with the user
    card.addEventListener('click', () => {
        in_conversation_with = _user.account_id;
        recipient_user = _user;
        console.log('In conversation with: ', in_conversation_with);
        loadMessages(_user.account_id);
        togglePageVisibility();
    })

    return card;
}

function togglePageVisibility() {
    conversationsPage.classList.toggle('!hidden');
    messagesPage.classList.toggle('!hidden');
}


// All function to send request to the server

export function loginCommand(token) {
    return JSON.stringify({
        command: "LOGIN",
        token: token,
    })
}

export function sendMessageCommand(token, message, receiverId) {
    return JSON.stringify({
        command: "SEND_MSG",
        token: token,
        content: message,
        receiver: receiverId
    })
}

export function updateMessageCommand(token, messageId, newContent) {
    return JSON.stringify({
        command: "UPDT_MSG",
        token: token,
        message_id: messageId,
        content: newContent
    })
}

export function deleteMessageCommand(token, messageId) {
    return JSON.stringify({
        command: "DEL_MSG",
        token: token,
        message_id: messageId
    })
}

// Get all messages during the session of a 
// conversation with a user
export function getNewAvailableMessageCommand(token) {
    return JSON.stringify({
        command: "NEW_MSG_AVAILABLE",
        token: token,
    })
}

// Get information about a specific user
export function userInfoCommand(token, userId) {
    return JSON.stringify({
        command: "USER_INFO",
        token: token,
        user_id: parseInt(userId)
    })
}

// Send info to the server about us
export function clientInfoCommand(token, isWriting, inConversationWith) {
    return JSON.stringify({
        command: "CLIENT_INFO",
        token: token,
        is_writing: isWriting,
        in_conversation_with: parseInt(inConversationWith)
    })
}