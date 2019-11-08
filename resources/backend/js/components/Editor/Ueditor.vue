<template>
    <div ref="editor" style="min-height:300px;"></div>
</template>

<script>
    import "@admin/assets/ue/ueditor.config";
    import "@admin/assets/ue/ueditor.all";
    import "@admin/assets/ue/lang/zh-cn/zh-cn";

    export default {
        mounted() {
            this.$nextTick(() => {
				this.$refs.editor.id = this.id;
				this.editor = this.UE.getEditor(this.id);
				this.editor.ready(() => {
                this.uploadParams = this.$store.getters.uploadParams;
                    if(this.uploadParams.uploadUrl == undefined){
                        this.$store.dispatch('getUploadParams').then(resp=>{
                            this.uploadParams = resp;
                            this.init();
                            this.onContentChange();
                            this.onFocus();
                        });
                    } else {
                        this.init();
                        this.onContentChange();
                        this.onFocus();
                    }	
				});
			});
        },
        props: {
            value:{
                type: String,
				default: ''
            }
		},
        data() {
            return {
                id: 'ue_' + Math.random(10),
                UE: window.UE || null,
                isInited: false,
				editor: null,
				_value: '',
                focus: false,
            }
        },
        methods:{
            init() {
				setTimeout(() => {
                    this.editor.setOpt("qiniuUploadUrl", this.$store.getters.uploadParams.uploadUrl);
                    this.editor.setOpt("qiniuUrlPrefix", this.$store.getters.uploadParams.cdnPrefix);
                    this.editor.setOpt("qiniuToken", (refresh)  => {
                        return this.$store.getters.uploadParams.uploadToken;
                    });
                    this.editor.setOpt('videoFieldName', 'file');
					this.setContent();
					this.$emit('on-editor-ready');
				}, 800);
			},
            setContent(value) {
                var value = value || this.value || this._value || '';
				this.editor.setContent(value);
                this.isInited = true;
			},
			execCommand(name, value, dir) {
				this.editor.execCommand(name, value, dir);
			},
			getContent(type, fn) {
				type = type || 'Content';
				return this.editor[`get${type}`](fn);
			},
			/**
			 * 监听ueditor 编辑器内容更改，返回给editor-component
			 * @author yiwuyu
			 */
			onContentChange() {
				this.editor.addListener('contentChange', function() {
					this._value = this.editor.getContent();
                    this.$emit('input', this._value);
				}.bind(this));
			},
			bindScrollEvent() {
				if (window.addEventListener) {
					window.addEventListener('scroll', this.onScroll, false);
				} else if (window.attachEvent) {
					window.attachEvent('scroll', this.onScroll);
				} else {
					window['onscroll'] = this.onScroll;
				}
			},
            onFocus() {
				this.editor.addListener('focus', function()  {
					this.focus = true;
				}.bind(this));
				this.editor.addListener('blur', function()  {
					this.focus = false;
				}.bind(this));
			},
        },
        watch:{
            value(value){
                if (!this.focus && this.isInited) {
                    this.setContent(value);
                }
            }
        }
    }
</script>