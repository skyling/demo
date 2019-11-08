<template>
    <div>
        <el-upload :action="uploadParams.uploadUrl||'#'"
                   :list-type="listType"
                   :data="{token: uploadParams.uploadToken}"
                   :file-list="fileList"
                   :accept="accept"
                   :limit="count"
                   :multiple="multi"
                   :on-success="onUploadSuccess"
                   :on-exceed="onExceed"
                   :on-error="onError"
                   :on-remove="onRemove"
                   :on-preview="onPreview"
                   :before-upload="onUploadBefore">
            <i class="el-icon-plus avatar-uploader-icon"></i>
            <div class="el-upload__tip" slot="tip" v-show="tip">{{ tip }}</div>
        </el-upload>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex'
    export default {
        name: 'uploadFile',
        props: {
            'value':{}, //默认封面图
            'tip':{'default':'点击列表标题查看'},
            'accept':{}, // 接受的类型
            'listType':{'default':'text'},
            'multi': {'default':false}, // 多张
            'count':{'default':0},
        },

        data() {
            return {
                fileList: [],
                initFlag: false,
            };
        },

        computed: {
            ...mapGetters([
                'uploadParams'
            ])
        },
        watch: {
            value(value) {
                if (!value || this.initFlag) return;
                //兼容处理，后台默认只保存图片名称
                this.fileList = [];
                if (_.isString(value)) {
                    value = [value];
                }
                value.forEach(item => {
                    if (/:\/\//.test(item)) {
                        this.fileList.push({
                            name: item,
                            url: item,
                            key: item,
                        });
                    } else {
                        //拼接cdn前缀
                        this.fileList.push({
                            name: item,
                            url: this.uploadParams.cdnPrefix + item,
                            key: item,
                        });
                    }
                });
                this.initFlag = true;
            }
        },

        methods: {
            onPreview(file) {
                window.open(file.url);
            },
            //上传成功
            onUploadSuccess(ret, file, fileList) {
                var key = ret.key;
                if (/:\/\//.test(key)) {
                    file.url = key;
                    file.key = key;
                } else {
                    //拼接cdn前缀
                    file.url = this.uploadParams.cdnPrefix + key;
                    file.key = key;
                }
                this.fileList = this.multi ? fileList : [file];
                this.handleSetValue();
            },
            handleSetValue(){
                this.initFlag = true;
                var value = [];
                this.fileList.forEach((item) => {
                    value.push(item['key']);
                });
                var input = this.multi ? value : value.pop();
                this.$emit('input', input);
            },

            //图片删除
            onRemove(file, fileList) {
                this.fileList = fileList;
                this.handleSetValue();
            },
            onExceed(files, fileList) {
                this.$message.error(`最多只能上传${this.count}个文件`);
            },
            onError(err, response, file) {
                this.$store.dispatch('refreshUploadParams').catch(e => {
                    console.log('refreshUploadParams error');
                    this.$message.error(e.message);
                });
                this.$message.error('上传失败，请重试');
            },
            //上传前检查数据
            onUploadBefore(file) {
                if (!this.uploadParams.uploadUrl || !this.uploadParams.uploadToken) {
                    this.$message.error('上传组件初始化失败，请刷新页面或联系管理员');
                    return Promise.reject(false);
                }
                return Promise.resolve(true);
            }
        }
    }
</script>
