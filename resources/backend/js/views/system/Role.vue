<template>
    <div class="app-container calendar-list-container">
        <div class="filter-container">
            <el-button v-if="$store.getters.hasPermission('role-create')"  class="filter-item" style="margin-left: 10px;" @click="handleCreate" type="primary"
                       icon="el-icon-edit">添加
            </el-button>
        </div>

        <el-table :data="list" v-loading="listLoading" element-loading-text="正在努力加载..." border fit highlight-current-row style="width: 100%">
            <el-table-column align="center" label="序号" width="65" prop="id"></el-table-column>
            <el-table-column align="center" label="名称" prop="name"></el-table-column>
            <el-table-column label="创建时间" prop="created_at"></el-table-column>
            <el-table-column label="更新时间" prop="updated_at"></el-table-column>
            <el-table-column align="left" label="操作" class-name="small-padding fixed-width">
                <template slot-scope="scope">
                    <a v-if="$store.getters.hasPermission('role-update')" class="text-primary" @click="handleUpdate(scope.row)">修改</a>
                    <a v-if="$store.getters.hasPermission('role-delete') && scope.row.name!='admin'" class="text-danger" @click="handleDelete(scope.row.id)">删除</a>
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
    </div>
</template>

<script>
    import { getRoles,deleteRole } from '@admin/api/system'
    export default {
        data() {
            return {
                listLoading:false,
                list:null,
                total:null,
                listQuery:{
                    page:1,
                    limit:20,
                },
            }
        },
        created() {
           this.getList();
        },
        methods:{
            getList() {
                this.listLoading = true;
                getRoles(this.listQuery).then(data => {
                    this.list = data.data;
                    this.total = data.total;
                    this.listLoading = false
                });
            },
            handleDelete(id) {
                this.$confirm('确认删除吗?', '提示', {
                    type: 'warning'
                }).then(() => {
                    return deleteRole(id)
                }).then(resp =>  {
                    this.getList();
                }).catch(e => {
                    console.log(e)
                });
            },
            handleUpdate(row) {
                this.$router.push('role/'+row.id+'/update');
            },
            handleCreate() {
                this.$router.push('role/create')
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
