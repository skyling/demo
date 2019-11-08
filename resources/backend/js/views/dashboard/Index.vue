<template>
    <div class="app-container">
        <el-form label-width="160px">
            <el-form-item label="页眉">
                <el-input value="秘密"></el-input>
            </el-form-item>
            <el-form-item label="标题">
                <el-input value="武警湖南总队训练基地呈批件"></el-input>
            </el-form-item>
            <el-form-item label="副标题">
                <el-input value="训基呈[2019]"></el-input>
            </el-form-item>
            <el-form-item label="正文标题">
                <el-input value="请示"></el-input>
            </el-form-item>
            <el-form-item label="正文内容">
                <ueditor v-model="content"></ueditor>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click="test">提交</el-button>
            </el-form-item>

        </el-form>

    </div>
</template>

<script>
    import UploadImage from '@admin/components/Upload/Image'
    // import EditorWang from '@admin/components/Editor/Wang'
    import Ueditor from "@admin/components/Editor/Ueditor"

    export default {
        components: {
            UploadImage,
            // EditorWang,
            Ueditor,
        },
        data() {
            return {
                content: '尊敬的领导：',
                content1: '1',
                image: null,
            }
        },
        methods: {
            hello() {
                console.log(this.content);
            },
            test() {
                this.$message.success("提交成功")
            }
        }
    }
</script>
