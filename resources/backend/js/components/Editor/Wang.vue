<template>
    <div>
        <div id="editorElem" style="text-align:left"></div>
    </div>
</template>

<script>
    import 'qiniu-js'
    import E from 'wangeditor'
    export default {
        name: 'editor',
        props:{
            'value':{'default':''}
        },
        computed:{
        },
        data() {
            return {
                uploadParams:null,
            }
        },
        methods: {
            getContent: function () {
                return this.value;
            },
            initEditor() {
                var editor = new E('#editorElem')
                editor.customConfig.qiniu = true
                editor.create();
                this.uploadInit(editor);
            },
            uploadInit(editor) {
                // 获取相关 DOM 节点的 ID
                    var btnId = editor.imgMenuId;
                    var containerId = editor.toolbarElemId;
                    var textElemId = editor.textElemId;
                    // 创建上传对象
                    var uploader = Qiniu.uploader({
                        runtimes: 'html5,flash,html4',    //上传模式,依次退化
                        browse_button: btnId,       //上传选择的点选按钮，**必需**
                         uptoken : this.uploadParams.uploadToken,
                         unique_names: true,
                         save_key: true,
                        domain: this.uploadParams.cdnPrefix,
                        container: containerId,           //上传区域DOM ID，默认是browser_button的父元素，
                        max_file_size: '100mb',           //最大文件体积限制
                        flash_swf_url: '../js/plupload/Moxie.swf',  //引入flash,相对路径
                        filters: {
                                mime_types: [
                                  //只允许上传图片文件 （注意，extensions中，逗号后面不要加空格）
                                  { title: "图片文件", extensions: "jpg,gif,png,bmp" }
                                ]
                        },
                        max_retries: 3,                   //上传失败最大重试次数
                        dragdrop: true,                   //开启可拖曳上传
                        drop_element: textElemId,        //拖曳上传区域元素的ID，拖曳文件或文件夹后可触发上传
                        chunk_size: '4mb',                //分块上传时，每片的体积
                        auto_start: true,                 //选择文件后自动上传，若关闭需要自己绑定事件触发上传
                        init: {
                            FileUploaded: (up, file, info) => {
                                var domain = this.uploadParams.cdnPrefix;
                                var res = JSON.parse(info.response);
                                var sourceLink = domain + res.key;
                                editor.cmd.do('insertHtml', '<img src="' + sourceLink + '" style="max-width:100%;"/>')
                            },
                            Error: (up, err, errTip) => {
                                this.$message.error('出错了,请稍后重试');
                            }
                        }
                    })
            }
        },
        mounted() {

            this.uploadParams = this.$store.getters.uploadParams;
            if(this.uploadParams.uploadUrl == undefined){
                this.$store.dispatch('getUploadParams').then(resp=>{
                    this.uploadParams = resp;
                    this.initEditor();
                });
            } else {
                this.initEditor();
            }

//            this.editorConfig(editor);

    },



    }
</script>

<style scoped>
</style>
