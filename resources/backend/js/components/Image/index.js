import Vue from 'vue';
import ImageComponent from './Image.vue';

export default {
    install(){
        Vue.component('el-image', ImageComponent);
    }
}