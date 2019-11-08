<template>
    <div class="app-container calendar-list-container">
        <div class="filter-container">
            <el-button v-if="$store.getters.hasPermission('account-create')" class="filter-item" style="margin-left: 10px;" @click="handleCreate" type="primary" icon="el-icon-edit">
                添加
            </el-button>
        </div>
        <el-table :data="list" v-loading="listLoading" element-loading-text="正在努力加载..." border fit highlight-current-row style="width: 100%">
            <el-table-column align="center" label="ID" width="65" prop="id"></el-table-column>
            <el-table-column align="center" label="用户名" prop="username"></el-table-column>
            <el-table-column label="邮箱" prop="email"></el-table-column>
            <el-table-column label="角色" prop="role"></el-table-column>
            <el-table-column label="创建时间" prop="created_at"></el-table-column>
            <el-table-column label="更新时间" prop="updated_at"></el-table-column>
            <el-table-column label="状态" prop="status">
                <template slot-scope="scope">
                    <span v-if="scope.row.id>1 && $store.getters.hasPermission('account-update')">
                        <a :class="scope.row.status==1 ? 'text-primary' :'text-danger'" @click="changeStatus(scope.row)">{{scope.row.status|statusFilter}}</a>
                    </span>
                    <span v-else>
                        {{scope.row.status|statusFilter}}
                    </span>
                </template>
            </el-table-column>
            <el-table-column align="left" label="操作" class-name="small-padding fixed-width">
                <template slot-scope="scope">
                    <a v-if="$store.getters.hasPermission('account-update') && scope.row.id!='1'" class="text-primary" @click="handleUpdateShop(scope.row)">店铺权限</a>
                    <a v-if="$store.getters.hasPermission('account-update')" class="text-primary" @click="handleUpdate(scope.row)">修改</a>
                    <a v-if="$store.getters.hasPermission('account-delete') && scope.row.id!='1'" class="text-danger" @click="handleDelete(scope.row.id)">删除</a>
                </template>
            </el-table-column>
        </el-table>
        <div class="pagination-container">
            <el-pagination background @size-change="handleSizeChange" @current-change="handleCurrentChange"
                           :current-page.sync="listQuery.page"
                           :page-sizes="[10,20,30, 50]" :page-size="listQuery.limit"
                           layout="total, sizes, prev, pager, next, jumper" :total="total">
            </el-pagination>
        </div>
        <el-dialog title="店铺权限" :visible.sync="dialogVisible" width="30%">
            <el-tree
                    :data="shops"
                    show-checkbox
                    node-key="id"
                    ref="tree"
                    :default-checked-keys="form.shop_id"
                    :props="{label:'name'}">
            </el-tree>
            <span slot="footer" class="dialog-footer">
                <el-button @click="dialogVisible = false">取 消</el-button>
                <el-button type="primary" @click="onSubmit" :loading="submitLoading">确 定</el-button>
            </span>
        </el-dialog>
    </div>
</template>

<script>
    import {getAccounts, deleteAccount, updateAccount} from '@admin/api/system'

    export default {
        data() {
            return {
                listLoading: false,
                dialogVisible: false,
                submitLoading: false,
                shops: [],
                list: null,
                total: null,
                checkAll: false,
                form: {
                    id: null,
                    shop_id: [],
                },
                listQuery: {
                    page: 1,
                    limit: 20,
                },
            }
        },
        created() {
            this.getList();
        },
        methods: {
            onSubmit() {
                this.form.shop_id = this.$refs.tree.getCheckedKeys();
                this.submitLoading = true;
                updateAccount(this.form.id, this.form).then(res => {
                    this.getList();
                    this.dialogVisible = false;
                }).finally(() => {
                    this.submitLoading = false;
                })
            },
            handleUpdateShop(row) {
                this.form.id = row.id;
                this.form.shop_id = row.shop_id;
                this.dialogVisible = true;
            },
            changeStatus(row) {
                const data = {status: row.status};
                data.status = Math.abs(row.status - 1);
                updateAccount(row.id, data).then(resp => {
                    row.status = data.status;
                })
            },
            getList() {
                this.listLoading = true;
                getAccounts(this.listQuery).then(data => {
                    this.list = data.data;
                    this.total = data.total;
                    this.listLoading = false
                });
            },
            handleDelete(id) {
                this.$confirm('确认删除吗?', '提示', {
                    type: 'warning'
                }).then(() => {
                    return deleteAccount(id)
                }).then(resp => {
                    this.getList();
                }).catch(e => {
                    console.log(e)
                });
            },
            handleUpdate(row) {
                this.$router.push('account/' + row.id + '/update');
            },
            handleCreate() {
                this.$router.push('account/create')
            },
            handleFilter() {
                this.listQuery.page = 1;
                this.getList();
            },
            handleSizeChange(val) {
                this.listQuery.limit = val;
                this.getList();
            },
            handleCurrentChange(val) {
                this.listQuery.page = val;
                this.getList();
            },
        }
    }
</script>
