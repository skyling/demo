<template>
    <div>
        <el-upload :action="uploadParams.uploadUrl||'#'"
                   :list-type="listType"
                   :data="{token: uploadParams.uploadToken}"
                   :file-list="imgList"
                   :limit="count"
                   :multiple="multi"
                   :on-success="onUploadSuccess"
                   :on-exceed="onExceed"
                   :on-preview="onPreview"
                   :on-error="onError"
                   :on-remove="onRemove"
                   :before-upload="onUploadBefore">
            <i class="el-icon-plus avatar-uploader-icon"></i>
            <div class="el-upload__tip" slot="tip">{{whRate ?'宽高比:'+whRate : ((width ? '宽度:'+width : '') +' '+ (height ?
                '高度:'+height : ''))}} {{ tip }}只能上传jpg/png文件，且不超过500kb
            </div>
        </el-upload>
        <el-dialog :visible.sync="dialogVisible" size="tiny">
            <img width="100%" :src="previewUrl" alt="">
        </el-dialog>
    </div>
</template>

<script>
    import {mapGetters} from 'vuex'

    export default {
        name: 'uploadImage',
        props: {
            'value': {}, //默认封面图
            'tip': {},
            'listType': {'default': 'picture-card'},
            'multi': {'default': false}, // 多张
            'width': {'default': 0}, //运行图片宽度 0表示不限制大小
            'height': {'default': 0},
            'whRate': {'default': 0}, // 宽高比,
            'count': {'default': 0},
        },
        data() {
            return {
                imgList: [],
                dialogImageUrl: '',
                dialogVisible: false,
                previewUrl: '',
                initFlag: false,
            };
        },

        computed: {
            ...mapGetters([
                'uploadParams'
            ])
        },
        watch: {
            value: {
                immediate: true,
                handler(value) {
                    // console.log('value', value);
                    if (!value || this.initFlag) {
                        this.initFlag = false;
                        return;
                    }
                    //兼容处理，后台默认只保存图片名称
                    if (_.isString(value)) {
                        value = [value];
                    }
                    this.imgList = value.map(item => {
                        if (/:\/\//.test(item)) {
                            return {name: item, url: item, key: item};
                        } else {
                            //拼接cdn前缀
                            return {name: item, url: this.uploadParams.cdnPrefix + item, key: item};
                        }
                    });
                },
            },
        },

        methods: {
            //上传成功
            onUploadSuccess(ret, file, fileList) {
                var key = ret.key;
                file.key = key;
                if (/:\/\//.test(key)) {
                    file.url = key;
                } else {
                    //拼接cdn前缀
                    file.url = this.uploadParams.cdnPrefix + key;
                }
                this.imgList = this.multi ? fileList : [file];
                this.handleSetValue();
            },
            handleSetValue() {
                this.initFlag = true;
                let value = this.imgList.map(item => {
                    return item.key;
                });
                let input = this.multi ? value : value.pop();
                this.$emit('input', input);
            },

            //图片删除
            onRemove(file, fileList) {
                this.imgList = fileList;
                this.handleSetValue();
            },

            //图片预览
            onPreview(file) {
                // console.log(this.imgList);
                this.previewUrl = /:\/\//.test(file.url) ? file.url : this.uploadParams.cdnPrefix + file.url;
                this.dialogVisible = true;
            },
            // 上传错误
            onError(err, response, file) {
                this.$store.dispatch('refreshUploadParams').catch(e => {
                    console.log('refreshUploadParams error');
                    this.$message.error(e.message);
                });
                this.$message.error('上传失败，请重试');
            },
            // 超过多少张
            onExceed(files, fileList) {
                this.$message.error(`最多只能上传${this.count}张图片`);
            },
            //上传前检查数据
            onUploadBefore(file) {
                if (!this.uploadParams.uploadUrl || !this.uploadParams.uploadToken) {
                    this.$message.error('上传组件初始化失败，请刷新页面或联系管理员');
                    return false;
                }
                return new Promise(function (resolve, reject) {
                    var image = new Image();
                    image.src = URL.createObjectURL(file);
                    image.onload = function () {
                        resolve({'width': image.width, 'height': image.height});
                    }
                }).then(data => {
                    var checkFile = true;
                    var w = data.width, h = data.height;
                    if (this.whRate) {
                        var r = w / h;
                        checkFile = checkFile && (r <= (this.whRate + 0.01) && r >= (this.whRate - 0.01));
                    } else {
                        if (this.width) {
                            checkFile = checkFile && (this.width == w);
                        }
                        if (this.height) {
                            checkFile = checkFile && (this.height == h);
                        }
                    }
                    if (!checkFile) {
                        var msg = '上传图片尺寸有误，请重新上传!' + (this.whRate ? ('宽高比为:' + this.whRate) : ((this.width ? '宽度:' + this.width : '') + ' ' + (this.height ? '高度:' + this.height : '')))
                        this.$message.error(msg);
                        return Promise.reject();
                    }
                    return Promise.resolve(true);
                })
            }
        }
    }
</script>
