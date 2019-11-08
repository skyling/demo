<template>
    <div class="app-container">
        <el-form ref="form" :rules="rules" :model="form" label-width="160px" @submit.prevent="onSubmit" style="margin:20px;width:80%;">
            <el-form-item label="角色名称" prop="name">
                <el-input v-model="form.name" :disabled="form.name=='admin'"></el-input>
            </el-form-item>
            <el-form-item label="权限" prop="permissions">
                <el-tree
                        :data="permissions"
                        node-key="name"
                        show-checkbox
                        :check-strictly="true"
                        @check-change="handleCheckChange"
                        :props="{label:'title',children:'subs',}"
                        ref="tree">
                </el-tree>
            </el-form-item>
            <el-form-item>
                <el-button @click="$router.back()">取消</el-button>
                <el-button type="primary" @click="onSubmit" :loading="loading">提交</el-button>
            </el-form-item>

        </el-form>
    </div>
</template>
<script>
    import {getPermissions, getRole, updateRole, createRole} from '@admin/api/system'

    export default {
//        name: 'roleForm',
        data() {
            return {
                loading: false,
                init: true,
                permissions: null,
                form: {
                    name: null,
                    permissions: null,
                },
                rules: {
                    name: [{required: true, message: '角色名称不能为空', trigger: 'blur'}],
                },
            }
        },
        computed: {},
        created() {
            this.getPermissions();
        },
        methods: {
            getPermissions() {
                this.loading = true;
                getPermissions().then(data => {
                    this.permissions = data;
                    this.getRole();
                    this.loading = false;
                });
            },
            getRole() {
                this.loading = true;
                if (this.$route.params.id == undefined) return;
                getRole(this.$route.params.id).then(data => {
                    this.form = data;
                    this.$refs.tree.setCheckedKeys(data.permissions, true);
                    this.loading = false;
                }).then(() => {
                    this.init = false;
                });
            },
            handleCheckChange(data, checked, indeterminate, rev) {
                console.log(data, checked, indeterminate, rev)
                if (this.$route.params.id && this.init) {
                    return;
                }
                if (data.subs && data.subs.length > 0) {
                    rev = true;
                    data.subs.forEach(item => {
                        this.handleCheckChange(item, checked, indeterminate, rev);
                    })
                }
                console.log(rev, checked)
                if (rev) {
                    this.$refs.tree.setChecked(data.name, checked);
                }

            },
            onSubmit() {
                this.$refs.form.validate(valid => {
                    if (!valid) return false;
                    this.form.permissions = this.$refs.tree.getCheckedKeys();
                    // 编辑
                    if (this.$route.params.id) {
                        updateRole(this.$route.params.id, this.form).then(resp => {
                            this.$router.back();
//                        this.$store.dispatch('closeViews', this);
                        }).catch(e => {
                            console.log(e);
                        });
                    } else { // 新增
                        createRole(this.form).then(resp => {
                            this.$router.back();
//                        this.$store.dispatch('closeViews', this);
                        }).catch(e => {
                            console.log(e);
                        })
                    }
                })

            },
        }
    }
</script>
