import Cookies from 'vue-cookie'
import { getUploadParams,refreshUploadParams } from '@admin/api/system'

const app = {
    state: {
        sidebar: {
            opened: !+Cookies.get('sidebarStatus')
        },
        uploadParams:{},
        language: Cookies.get('language') || 'zh' //默认中文
    },
    mutations: {
        TOGGLE_SIDEBAR: state => {
            if (state.sidebar.opened) {
                Cookies.set('sidebarStatus', 1)
            } else {
                Cookies.set('sidebarStatus', 0)
            }
            state.sidebar.opened = !state.sidebar.opened
        },
        SET_LANGUAGE: (state, language) => {
            state.language = language
            Cookies.set('language', language)
        },
        SET_UPLOAD_PARAMS: (state, uploadParams) => {
            state.uploadParams = uploadParams
        },
    },
    actions: {
        toggleSideBar({ commit }) {
            commit('TOGGLE_SIDEBAR')
        },
        setLanguage({ commit }, language) {
            commit('SET_LANGUAGE', language)
        },
        getUploadParams({ commit }) {
            return new Promise((resolve, reject) => {
                getUploadParams().then(data => {
                    commit('SET_UPLOAD_PARAMS', data)
                    resolve(data)
                }).catch(error => {
                    reject(error)
                })
            })
        },
        refreshUploadParams({ commit }) {
            return new Promise((resolve, reject) => {
                refreshUploadParams().then(data => {
                    commit('SET_UPLOAD_PARAMS', data)
                    resolve(data)
                }).catch(error => {
                    reject(error)
                })
            })
        }
    }
}

export default app
