const AUTH_TOKEN_KEY = 'warehouse_auth_token';
const AUTH_USER_KEY = 'warehouse_auth_user';

export const getStoredAuth = () => {
    const token = localStorage.getItem(AUTH_TOKEN_KEY);
    const rawUser = localStorage.getItem(AUTH_USER_KEY);
    let user = null;

    if (rawUser) {
        try {
            user = JSON.parse(rawUser);
        } catch {
            user = null;
        }
    }

    return { token, user };
};

export const storeAuth = ({ token, user }) => {
    localStorage.setItem(AUTH_TOKEN_KEY, token);
    localStorage.setItem(AUTH_USER_KEY, JSON.stringify(user));
};

export const clearAuth = () => {
    localStorage.removeItem(AUTH_TOKEN_KEY);
    localStorage.removeItem(AUTH_USER_KEY);
};
