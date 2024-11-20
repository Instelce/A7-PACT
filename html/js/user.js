// Import this file to get the authenticated user's data.

export async function getUser() {
    return fetch('/api/auth/user')
        .then(response => response.json())
        .then(data => {
            return data;
        });
}