import { login, logout, getUserInfo,refreshToken } from '@admin/api/login'
import { getToken, setToken, removeToken } from '@admin/utils/auth'

const user = {
    state: {
        name: '',
        permissions: [],
    },

    mutations: {
        SET_NAME: (state, name) => {
            state.name = name
        },
        SET_PERMISSIONS: (state, permissions) => {
            state.permissions = permissions
        }
    },

    actions: {
        login({ commit }, userInfo) {
            const email = userInfo.email.trim()
            return new Promise((resolve, reject) => {
                return login(email, userInfo.password).then(data => {
                    setToken(data)
                    resolve()
                }).catch(error => {
                    reject(error)
                })
            })
        },

        // 获取用户信息
        getUserInfo({ commit, state }) {
            return new Promise((resolve, reject) => {
                getUserInfo().then(data => {
                    commit('SET_NAME', data.username)
                    commit('SET_PERMISSIONS', data.permissions)
                    resolve(data)
                }).catch(error => {
                    reject(error)
                })
            })
        },

        // 登出
        logout({ commit, state }) {
            return new Promise((resolve, reject) => {
                removeToken();
                logout();
                resolve();
            })
        },
    }
}

export default user
