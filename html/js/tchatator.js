import {getUser} from "./user.js";

let appEnv = document.querySelector('input.app-environment').value;
let domain = window.location.hostname;

// ---------------------------------------------------------------------------------------------- //
// Tchatator socket client that connects to the C server
// Host: localhost
// Port: 4242
// ---------------------------------------------------------------------------------------------- //

// Member client

export const REFRESH_RATE = 2000;

let is_writing = false;
let in_conversation_with = null;
let recipient_is_writing = false;
let recipient_is_connected = false;
let recipient_present_in_conversation = false;
let user = null;
let recipient_user = null;
let socket;
let contacts = [];


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
let gotoConversationsButton;
let contactProfessionalButton = document.querySelectorAll('.contact-professsional');

if (chat) {
    conversationsPage = chat.querySelector('.conversations-page');
    messagesPage = chat.querySelector('.messages-page');
    conversationsContainer = chat.querySelector('.conversations-gen');
    chatTrigger = document.querySelectorAll('.chat-trigger');

    // Check window size
    if (window.innerWidth < 768) {
        chatTrigger = chatTrigger[1];
        contactProfessionalButton = contactProfessionalButton[0];
    } else {
        chatTrigger = chatTrigger[0];
        contactProfessionalButton = contactProfessionalButton[1];
    }

    messagesContainer = chat.querySelector('.messages-container');
    messageWriter = chat.querySelector('#message-writer');
    sendButton = chat.querySelector('.send-button');
    writingIndicator = chat.querySelector('.writing-indicator');
    gotoConversationsButton = chat.querySelector('.goto-conversations');

    // For offer detail page
    if (contactProfessionalButton) {
        let professionalId = contactProfessionalButton.getAttribute('data-pro-id');

        contactProfessionalButton.addEventListener('click', () => {
            in_conversation_with = professionalId;

            fetch(`/api/users/${professionalId}`)
                .then(r => r.json())
                .then(professional => {
                    chat.classList.remove('hidden');

                    recipient_user = professional

                    console.log('In conversation with: ', in_conversation_with);

                    loadMessages(professionalId);
                    togglePageVisibility();

                    // Set the recipient user
                    let recipientName = document.querySelector('.recipient-name');
                    let recipientAvatar = document.querySelector('.recipient-avatar');

                    recipientName.innerText = recipient_user.name;
                    recipientAvatar.src = recipient_user.avatar_url;
                })
        })
    }

    // Close chat when clicking outside
    window.addEventListener('click', (e) => {
        // if (!chat.contains(e.target) && !chatTrigger.contains(e.target)) {
        //     chat.classList.add('hidden');
        // }
    })
} else {
    if (contactProfessionalButton) {
        contactProfessionalButton.innerHTML = 'Connectez-vous pour contacter un professionnel';
        setTimeout(() => {
            contactProfessionalButton.innerHTML = 'Contacter un professionnel';
        })
    }
}


getUser().then(_user => {
    user = _user;

    if (user && user.type !== 'professional') {
        if (appEnv == 'dev') {
            socket = new WebSocket(`ws://${domain}:4242`);
        } else {
            socket = new WebSocket(`wss://${domain}:4242`);
        }

        if (socket.readyState === WebSocket.CLOSED) {
            console.log('Websocket connection failed');
        } else {
            console.log('Websocket connection successful');

            socket.addEventListener("open", () => {
                console.log('Connected to the server');

                // Send login request
                socket.send(loginCommand(user.api_token));
            });

            socket.addEventListener("message", (event) => {
                console.log('')

                let data = JSON.parse(event.data);

                if (data.command === 'SEND_MSG') {
                    data.message.modified_date = null;
                    messagesContainer.appendChild(messageCard(socket, data.message, user, recipient_user));
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
                                console.log("NEW MESSAGE", change.message, in_conversation_with);
                                // If exist and not in the conversation set the last message
                                if (in_conversation_with != change.message.sender_id) {
                                    setLastMessage(change.message.sender_id, change.message.content);
                                }

                                if (in_conversation_with == change.message.sender_id) {
                                    change.message.modified_date = null; // Why ?
                                    messagesContainer.appendChild(messageCard(socket, change.message, user, recipient_user));
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
                                newModifiedText.innerText = 'Modifié il y a ' + formatDate(change.message.modified_date);
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
            });

            socket.addEventListener("close", () => {
                console.log('Disconnected from the server');
            });

            socket.addEventListener("error", (error) => {
                console.log('Error: ', error);
            });

            setInterval(() => {
                // Send info to the server about us
                socket.send(clientInfoCommand(user.api_token, is_writing, in_conversation_with));

                // Get informations
                socket.send(getChangesCommand(user.api_token));
                if (in_conversation_with) {
                    socket.send(userInfoCommand(user.api_token, in_conversation_with));
                }
            }, REFRESH_RATE)
        }

        // Toggle chat
        chatTrigger.addEventListener('click', () => {
            chat.classList.toggle('hidden');
        })

        // Send message
        sendButton.addEventListener('click', () => {
            if (messageWriter.value !== '') {
                socket.send(sendMessageCommand(user.api_token, messageWriter.value, in_conversation_with));

                messageWriter.value = '';
                is_writing = false;

                // If the contact doesnt exist
                if (!contacts.find(contact => contact.account_id === in_conversation_with)) {
                    fetch(`/api/users/${in_conversation_with}`)
                        .then(r => r.json())
                        .then(contact => {
                            conversationsContainer.appendChild(contactCard(contact));
                            contacts.push(contact);
                        })
                }
            }
        })

        // Send info when typing in the message writer
        messageWriter.addEventListener('input', () => {
            is_writing = messageWriter.value !== '';
        })

        // Load conversations at the beginning
        loadConversations(conversationsContainer);

        // Go back to the conversations
        gotoConversationsButton.addEventListener('click', () => {
            togglePageVisibility();
            in_conversation_with = null;
            is_writing = false;
        })
    }
})

function loadConversations() {
    fetch('/api/messages')
        .then(r => r.json())
        .then(users => {
            users.forEach(user => {
                conversationsContainer.appendChild(contactCard(user));
                contacts.push(user);
            })
        })
}

// On start load contacts
fetch('/api/messages')
    .then(r => r.json())
    .then(users => {
        users.forEach(user => {
            contacts.push(user);
        })
    })


function loadMessages(receiverId) {
    messagesContainer.innerHTML = '';

    fetch(`/api/messages/${receiverId}`)
        .then(r => r.json())
        .then(messages => {
            let currentDay = null;
            messages.forEach(message => {
                // Add day separator
                let messageDate = new Date(message.sended_date);
                let day = messageDate.getDate();

                if (currentDay !== day) {
                    let daySeparator = document.createElement('div');
                    daySeparator.classList.add('day-separator');
                    daySeparator.innerText = `${day} ${messageDate.toLocaleString('default', {month: 'long'})} ${messageDate.getFullYear()}`;
                    messagesContainer.appendChild(daySeparator);

                    currentDay = day;
                }

                messagesContainer.appendChild(messageCard(socket, message, user, recipient_user));
            })

            // Scroll to the bottom
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        })
}

export function messageCard(socket, message, user, recipient_user, dashboard = false) {
    let sent = message.sender_id === user.account_id;

    let card = document.createElement('div');
    card.setAttribute('data-id', message.id);

    // Add classes
    card.classList.add('message');
    card.classList.add(sent ? 'sent' : 'received');
    if (message.deleted) {
        card.classList.add('deleted');
    }
    if (dashboard) {
        card.classList.add('dashboard');
    }

    // Modify date format
    let dateText = '';
    if (message.modified_date) {
        dateText = formatDate(message.modified_date);
    }

    card.innerHTML = `
        <div class="message-content">
            ${!sent ? `<small>${recipient_user.name}</small>` : '<small>Vous</small>'}
            ${message.deleted ? `<p>Ce message à été supprimé</p>` : `<pre>${message.content}</pre>`}
            ${message.modified_date && !message.deleted ? `<small class="modified">Modifié il y a ${dateText}</small>` : ''}
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

            // Remove modified text is exists
            let modifiedText = card.querySelector('.modified');
            if (modifiedText) {
                card.querySelector('.message-content').removeChild(modifiedText);
            }
        })

        // Toggle update form
        let toggleUpdateForm = card.querySelector('.update-message');

        toggleUpdateForm.addEventListener('click', () => {
            // Remove message content and hide buttons
            messageContent.innerHTML = '';
            buttons.classList.add('!hidden');

            let updateForm = document.createElement('div');
            updateForm.classList.add('update-form');

            // Add input and update button
            let updateInput = document.createElement('textarea');
            updateInput.value = message.content;
            updateInput.classList.add('update-input');
            updateForm.appendChild(updateInput);

            let updateButton = document.createElement('button');
            updateButton.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check ml-2"><path d="M20 6 9 17l-5-5"/></svg>';
            updateButton.classList.add('update-button');
            updateForm.appendChild(updateButton);

            // Remove modified text is exists
            let modifiedText = card.querySelector('.modified');
            if (modifiedText) {
                card.querySelector('.message-content').removeChild(modifiedText);
            }

            updateButton.addEventListener('click', () => {
                messageContent.innerHTML = updateInput.value;
                socket.send(updateMessageCommand(user.api_token, message.id, updateInput.value));

                // Remove input and button
                card.querySelector('.message-content').removeChild(updateForm);
                buttons.classList.remove('!hidden');

                // Add modified text
                let newModifiedText = document.createElement('small');
                newModifiedText.classList.add('modified');
                newModifiedText.innerText = 'Modifié il y a quelques secondes';
                card.querySelector('.message-content').appendChild(newModifiedText);
            })

            card.querySelector('.message-content').appendChild(updateForm);
        })
    }

    return card;
}

function contactCard(_user) {
    let card = document.createElement('article');
    card.classList.add('conversation-card');
    card.id = _user.account_id;
    card.innerHTML = `
            <a href="${_user.account_id}">
              <img src="${_user.avatar_url}" alt="profile picture">
              <span class="connected-badge"></span>
            </a>
            <div class="">
                <h3>${_user.name}</h3>
                <h6 class="last-message line-clamp-1">${user.account_id == _user.last_message.sender_id ? "Vous : " : ""}${_user.last_message.content}</h6>
            </div>
    `;

    // Switch to the conversation with the user
    card.addEventListener('click', () => {
        in_conversation_with = _user.account_id;
        recipient_user = _user;

        console.log('In conversation with: ', in_conversation_with);

        loadMessages(_user.account_id);
        togglePageVisibility();

        // Set the recipient user
        let recipientName = document.querySelector('.recipient-name');
        let recipientAvatar = document.querySelector('.recipient-avatar');

        recipientName.innerText = _user.name;
        recipientAvatar.src = _user.avatar_url;
    })

    return card;
}

export function setLastMessage(userId, message) {
    let lastMessage = document.querySelector(`.conversation-card[id="${userId}"] .last-message`);
    lastMessage.innerText = message;
}

function togglePageVisibility() {
    conversationsPage.classList.toggle('!hidden');
    messagesPage.classList.toggle('!hidden');
}

export function formatDate(date) {
    let dateText = '';
    let diff = new Date() - new Date(date);

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

    return dateText;
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

// Get changes
export function getChangesCommand(token) {
    return JSON.stringify({
        command: "NEW_CHG_AVAILABLE",
        token: token
    })
}
