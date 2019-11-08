import axios from 'axios'
import { Message } from 'element-ui'
import store from '@admin/store'
import { getToken,removeToken } from '@admin/utils/auth'

// create an axios instance
const service = axios.create({
  baseURL: '/admin', // api的base_url
  timeout: 3000000 // request timeout
})

service.interceptors.request.use(function (config) {
    let token = getToken()
    if (token) {
        config.headers.common['Authorization'] = 'Bearer '+ token;
    }
    return config;
}, function (error) {
    return Promise.reject(error);
});

service.interceptors.response.use(function (response) {
    if (response.data.msg) {
        Message({
            message: response.data.msg,
            type: 'success',
            duration: 5 * 1000
        })
    }
    return Promise.resolve(response.data);
}, function (error) {
    if (error.response.status == 401) {
        removeToken();
        var token = getToken();
        if (token){
            store.dispatch('logout').then(function(e){
                console.log(e);
            });
        }

        window.location.href='#/login';
    }

    let message = error.response.data.error || error.response.data.msg || error.response.data.message || error.message;
    if (error.response.status == 422) {
        let key = _.keys(error.response.data.errors).shift() || '';
        message = key ? error.response.data.errors[key].pop() : message;
    }

    if(error.response.status == 429 ) {
        message = '请求过于频繁,请稍后重试';
    }

    Message({
      message: message,
      type: 'error',
      duration: 5 * 1000
    });
    return Promise.reject(error);
});

export default service
