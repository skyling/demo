<template>
    <div class="app-container">
        <el-form ref="form" :rules="rules" :model="form" label-width="160px" @submit.prevent="onSubmit" style="margin:20px;width:80%;">
        	<el-form-item label="名称" prop="username">
                   <el-input v-model="form.username" :disabled="id=='1'"></el-input>
        	</el-form-item>
            <el-form-item label="邮箱" prop="email">
                <el-input v-model="form.email" type="email"></el-input>
            </el-form-item>
            <el-form-item label="密码" prop="password">
                <el-input v-model="form.password" type="password"></el-input>
            </el-form-item>
            <el-form-item label="确认密码" prop="rePassword">
                <el-input v-model="form.rePassword" type="password"></el-input>
            </el-form-item>
            <el-form-item label="角色" prop="role"  v-show="id!=1">
                <el-select v-model="form.role" placeholder="请选择">
                    <el-option
                            v-for="item in roles"
                            :key="item.name"
                            :label="item.name"
                            :value="item.name">
                    </el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="状态" prop="status" v-show="id!=1">
                <el-select v-model="form.status" placeholder="请选择">
                    <el-option
                            v-for="item in [{value:1, label:'启用'},{value:0, label:'禁用'},]"
                            :key="item.value"
                            :label="item.label"
                            :value="item.value">
                    </el-option>
                </el-select>
            </el-form-item>
        	<el-form-item>
                   <el-button @click="$router.back()">取消</el-button>
        		<el-button type="primary" @click="onSubmit" :loading="loading">提交</el-button>
        	</el-form-item>

        </el-form>
    </div>
</template>
<script>
    import { getSelectRoles,getAccount,updateAccount,createAccount } from '@admin/api/system'
    export default {
        data() {
            return {
                loading:false,
                roles:null,
                id:null,
                form:{
                    username:null,
                    email:null,
                    password:null,
                    rePassword:null,
                    role:null,
                    status:1,
                },
                rules:{
                    username: [{ required: true, message: '用户名称不能为空', trigger: 'blur' }],
                    email: [
                        {required: true, message: '邮箱不能为空', trigger: 'blur' },
                        {type:'email', message: '邮箱格式不正确', trigger: 'blur,change' },
                    ],
                    password: [{ required: true, message: '密码不能为空', trigger: 'blur' }],
                    rePassword: [
                        { required: true, message: '确认密码不能为空', trigger: 'blur' },
                        { validator : (rule, value, callback) => {
                            if(value!= this.form.password) {
                                return callback(new Error('确认密码和密码不一致'))
                            }
                            return callback();
                         }, trigger: 'blur,change'
                        },

                    ],
                    role:[{required:true, message:'请选择角色', trigger:'blur,change'}],
                    status:[{required:true, message:'请选择状态', trigger:'blur,change'}],
                },
            }
        },
        computed: {
        },
        created() {
            this.getRoles();
            this.getAccount();
        },
        methods:{
            getRoles() {
                getSelectRoles({page:1,limit:100}).then(resp => {
                    this.roles = resp;
                }).catch(e=>{console.log(e)})
            },
            getAccount(){
                if(this.$route.params.id == undefined || !this.$route.params.id) {
                    return;
                };
                delete this.rules.password;
                delete this.rules.rePassword[0];
                this.loading = true;
                this.id = this.$route.params.id;
                getAccount(this.$route.params.id).then(data=>{
                    this.form = data;
                    this.loading= false;
                })
            },
            onSubmit() {
                this.$refs.form.validate((valid) => {
                    if (!valid) { return false;}
                    // 编辑
                    if (this.$route.params.id) {
                        updateAccount(this.$route.params.id, this.form).then(resp=>{
                            this.$router.back();
                        }).catch(e => {
                            console.log(e);
                        });
                    } else { // 新增
                        createAccount(this.form).then(resp => {
                            this.$router.back();
                        }).catch(e => {
                            console.log(e);
                        })
                    }
                });
            },
        }
    }
</script>
