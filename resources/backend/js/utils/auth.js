import store from '../store/index'
import Cookies from 'vue-cookie'
import { refreshToken } from '@admin/api/login'
const TokenKey = 'admin_token';
const TokenExpiresInKey = 'expires_in';
export function getToken() {
    var expiresIn = Cookies.get(TokenExpiresInKey);
    if (expiresIn && expiresIn!=-1 && (expiresIn-(new Date).getTime()) < 1800000) {
        Cookies.set(TokenExpiresInKey, -1)
        refreshToken().then(data => {
            setToken(data)
        }).catch(error => {
            console.log(error)
        })
    }
    return Cookies.get(TokenKey)
}

export function setToken(data) {
    Cookies.set(TokenExpiresInKey, (new Date()).getTime() + data.expires_in * 1000);
    return Cookies.set(TokenKey, data.access_token);
}

export function removeToken() {
    Cookies.delete(TokenExpiresInKey);
    return Cookies.delete(TokenKey);
}
