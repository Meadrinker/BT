export const login = async (credentials) => {
    const response = await fetch('/api/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(credentials),
    });

    if (!response.ok) {
        throw new Error('');
    }

    const responseData = await response.json();
    return responseData;
}


export const fetchUserData = async (accessToken) => {
    const response = await fetch('/api/user', {
        method: 'GET',
        headers: {
            'Authorization': `Bearer ${accessToken}`,
            'Content-Type': 'application/json',
        },
    });

    if (!response.ok) {
        throw new Error('');
    }

    const userData = await response.json();
    return userData;
}