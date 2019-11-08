window._ = require('lodash');
try {
    window.$ = window.jQuery = require('jquery');
    require('bootstrap-sass');
} catch (e) {
}
//Promise.prototype.finally = function (callback) {
//  let P = this.constructor;
//  return this.then(
//    value  => P.resolve(callback()).then(() => value),
//    reason => P.resolve(callback()).then(() => { throw reason })
//  );
//};
window.Vue = require('vue');
import ElementUI from 'element-ui';
import i18n from './lang'
import * as filters from './filters'
import VueCookie from 'vue-cookie'
import 'element-ui/lib/theme-chalk/index.css';

Vue.use(VueCookie);
// 没用了 语言切换的不用了
Vue.use(ElementUI, {
    size: 'mini', // set element-ui default size
    i18n: (key, value) => i18n.t(key, value)
});

Object.keys(filters).forEach(key => {
    Vue.filter(key, filters[key])
})

import store from './store/index';
import router from './router/index'
import './error-log'
import './permission'

function hasPermission(vm, permission) {
    if (!vm.store.user.permissions) return false;
    return vm.store.user.permissions(permission) >= 0;
}

// 汇率准换
function exchangeRate(money) {
    return (money * 6.8).toFixed(2);
}

import Image from './components/Image/index';

Vue.use(Image);
Vue.prototype.hasPermission = hasPermission;
Vue.prototype.exchangeRate = exchangeRate;
Vue.prototype.dataPickerOptions = {
    shortcuts: [{
        text: '最近一周',
        onClick(picker) {
            const end = new Date();
            const start = new Date();
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);
            picker.$emit('pick', [start, end]);
        }
    }, {
        text: '最近一个月',
        onClick(picker) {
            const end = new Date();
            const start = new Date();
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
            picker.$emit('pick', [start, end]);
        }
    }, {
        text: '最近三个月',
        onClick(picker) {
            const end = new Date();
            const start = new Date();
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 90);
            picker.$emit('pick', [start, end]);
        }
    }]
};
// Vue.prototype.$ELEMENT = { size: 'mini', zIndex: 2000 };
require('promise.prototype.finally').shim();
const app = new Vue({
    el: '#app',
    router,
    store,
    i18n,
}).$mount('#app');

