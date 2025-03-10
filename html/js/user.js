// Import this file to get the authenticated user's data.

export async function getUser() {
    return fetch('/api/auth/user')
        .then(response => {
            if (response.status === 200) {
                return response.json()
            } else {
                return null
            }
        })
        .then(data => {
            return data;
        })
        .catch((e) => {
            console.log("Not connected")
        });
}