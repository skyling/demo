<template>
    <div class="app-container">
        <el-form label-width="160px">
            <el-form-item label="页眉">
                <el-input v-model="form.pageheader"></el-input>
            </el-form-item>
            <el-form-item label="标题">
                <el-input v-model="form.title"></el-input>
            </el-form-item>
            <el-form-item label="副标题">
                <el-input v-model="form.subtitle"></el-input>
            </el-form-item>
            <el-form-item label="正文标题">
                <el-input v-model="form.contenttitle"></el-input>
            </el-form-item>
            <el-form-item label="正文内容">
                <ueditor v-model="form.content"></ueditor>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click="test">提交</el-button>
            </el-form-item>

        </el-form>

    </div>
</template>

<script>
    import Ueditor from "@admin/components/Editor/Ueditor"
    import {submitContent} from "@admin/api/system"

    export default {
        components: {
            Ueditor,
        },
        data() {
            return {
                form: {
                    pageheader: '秘密',
                    title: '武警湖南总队训练基地呈批件',
                    subtitle: '训基呈[2019]',
                    contenttitle: '请示',
                    content: '尊敬的领导：',
                },
            }
        },
        methods: {
            hello() {
                console.log(this.content);
            },
            test() {
                submitContent(this.form).then(() => {
                    window.location.href = "/print"
                })
            }
        }
    }
</script>
