
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
        user_id: userId
    })
}

// Send info to the server about us
export function clientInfoCommand(token, isWritingAt, inConversationWith) {
    return JSON.stringify({
        command: "CLIENT_INFO",
        token: token,
        is_writing_at: isWritingAt,
        in_conversation_with: inConversationWith
    })
}