import router from './router'
import store from './store'
import NProgress from 'nprogress' // progress bar
import { getToken } from '@admin/utils/auth' // getToken from cookie
import { Message } from 'element-ui'



const whiteList = ['/login']// no redirect whitelist

router.beforeEach((to, from, next) => {
    NProgress.start() // start progress bar
    if (getToken()) { // 判断是否有token
        if (to.path === '/login') {
            next({ path: '/' })
            NProgress.done() // router在hash模式下 手动改变hash 重定向回来 不会触发afterEach 暂时hack方案 ps：history模式下无问题，可删除该行！
        } else {
            if (store.getters.permissions.length === 0) { // 判断当前用户是否已拉取完user_info信息
                // 拉取user_info
                store.dispatch('getUserInfo').then(resp => {
                    next({...to, replace:true});
                    // 七牛上传token
                    if (!store.getters.uploadParams.expiresIn || (store.getters.uploadParams.expiresIn-60)*1000 < (new Date()).getTime()) {
                        store.dispatch('getUploadParams').catch(e => {
                            console.log(e)
                        })
                    }
                }).catch((e) => {
                    store.dispatch('logout').then(() => {
                        next({ path: '/login' })
                    }).catch(() => {
                    })
                })
            } else {
                next();
            }
        }
    } else {
        if (whiteList.indexOf(to.path) !== -1) { // 在免登录白名单，直接进入
            next()
        } else {
            next('/login') // 否则全部重定向到登录页
            NProgress.done() // router在hash模式下 手动改变hash 重定向回来 不会触发afterEach 暂时hack方案 ps：history模式下无问题，可删除该行！
        }
    }
})

router.afterEach(() => {
    NProgress.done() // finish progress bar
})
