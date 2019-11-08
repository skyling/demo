<template>
    <span>
        <img @click="preview()" :src="url" :height="height" :width="width" :style="styles" v-show="url">
        <template>
            <el-dialog title="图片预览" size="large" :close-on-click-modal="false" ref="imgPreview" :visible.sync="dialogVisible" @close="closeDialog()">
                <div style="text-align:center;">
                    <img :src="url" style="max-width: 80%;">
                </div>
            </el-dialog>
        </template>
    </span>
</template>
<script>
    import { mapGetters } from 'vuex'
    export default {
        props: {'src':{default:''}, 'width':{'default':'auto'}, 'height':{'default':'auto'}, 'styles':{'default':'border: 1px solid darkgray; padding: 5px;margin-right: 5px;'}}, //接收父组件传递的默认值
        data() {
            return {
                dialogVisible: false,
                url:'',
            };
        },
        computed: {
            ...mapGetters([
                'uploadParams'
            ])
        },
        created() {
            this.url = this.src ? (/:\/\//.test(this.src) ? this.src : (this.uploadParams.cdnPrefix + this.src)) : '';
        },
        methods: {
            preview: function() {
                this.dialogVisible = true;
            },
            closeDialog:function(){
                this.dialogVisible = false;
            }
        },
        watch:{
            src: {
                handler: function (newVal, oldVal) {
                    this.url = newVal ? (/:\/\//.test(newVal) ? newVal : this.uploadParams.cdnPrefix + newVal) : (this.show_default ? '/backend/imgs/avatar.jpeg' : '');
                },
            }
        },
    }
</script>
