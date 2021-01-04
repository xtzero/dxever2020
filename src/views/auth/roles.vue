<template>
    <div class="container">
        <!-- 搜索 -->
        <div class="search-container">
            <div class="search-item">
                <a-input placeholder="请输入角色名" v-model="where.name"/>
            </div>
            <div class="search-item">
                <a-button type="primary" @click="getRoleList">
                    <a-icon type="search" />
                    搜索
                </a-button>
            </div>
            <div class="search-item">
                <a-button type="success" @click="onAddClick">
                    <a-icon type="plus" />
                    添加
                </a-button>
            </div>
        </div>
        <!-- 表格 -->
        <a-table :rowClassName="()=>'table-row'" class="table" :data-source="list" :loading="loading" :pagination="false" :rowKey="(record)=>record.id">
            <a-table-column title="序号" :customRender="(t, r, i) => i + 1" />
            <a-table-column title="角色名" data-index="name" />
            <a-table-column title="操作">
                <template slot-scope="row">
                    <span>
                        <a-button type="primary" @click="onEditClick(row)">
                            <a-icon type="edit" />
                            编辑
                        </a-button>
                        <a-divider type="vertical" />
                        <a-button type="danger" @click="onDeleteClick(row)">
                            <a-icon type="delete" />
                            删除
                        </a-button>
                        <a-divider type="vertical" />
                        <a-button type="primary" @click="onSetMenuClick(row)">
                            设置菜单
                            <a-icon type="right" />
                        </a-button>
                    </span>
                </template>
            </a-table-column>
        </a-table>
        <!-- 添加/编辑 -->
        <a-modal
            v-model="edit.display"
            :title="edit.id != 0 ? '编辑角色' : '新建角色'"
            ok-text="确认"
            cancel-text="取消"
            @ok="commitEdit"
            :ok-button-props="{ props: { loading: edit.loading} }"

        >
            <a-form :label-col="{ span: 5 }" :wrapper-col="{ span: 12 }">
                <a-form-item label="角色名">
                    <a-input
                        v-model="edit.data.name"
                        placeholder="角色名"
                    />
                </a-form-item>
            </a-form>
        </a-modal>
    </div>
</template>
<script>
import { roleList, saveRole, deleteRole } from '@/api/auth'
export default {
    name: 'role',
    data() {return {
        list: [],
        loading: false,
        where: {
            name: ''
        },
        edit: {
            display: false,
            id: false,
            oriData: {
                name: ''
            },
            data: {
                name: ''
            },
            loading: false
        }
    }},
    mounted() {
        this.getRoleList()
    },
    methods: {
        onSetMenuClick(row) {
            this.$router.push({
                path: '/roleMenus',
                query: {
                    id: row.id,
                    name: row.name
                }
            })
        },
        onAddClick() {
            this.edit.data = Object.assign({}, this.edit.oriData)
            this.edit.id = false
            this.edit.display = true
        },
        commitEdit() {
            const saveData = {}
            if (this.edit.data.name != '') {
                saveData.name = this.edit.data.name
            } else {
                this.$message.error('请输入角色名')
                return
            }
            if (this.edit.id) {
                saveData.id = this.edit.id
            }
            this.edit.loading = true
            saveRole(saveData).then(res => {
                if (res.code == 200) {
                    this.$message.success('保存成功')
                    this.edit.display = false
                    this.getRoleList()
                } else {
                    this.$message.error(res.msg)
                }
            }).catch(e => {
                console.error(e)
                this.$message.error('保存角色信息时出现问题')
            }).finally(() => {
                this.edit.loading = false
            })
        },
        onEditClick(row) {
            this.edit.id = row.id
            this.edit.data.name = row.name
            this.edit.display = true
        },
        onDeleteClick(row) {
            this.$confirm({
                content: `确定要删除角色 ${row.name} 吗？`,
                onOk:() => {
                    this.loading = true
                    deleteRole({
                        id: row.id
                    }).then(res => {
                        if (res.code == 200) {
                            this.$message.success('删除成功')
                            this.getRoleList()
                        } else {
                            this.$message.error(res.msg)
                        }
                    }).catch(e => {
                        console.error(e)
                        this.$message.error('删除时出现问题')
                    }).finally(() => {
                        this.loading = false
                    })
                },
                okText: '确定',
                cancelText: '取消'
          })
        },
        getRoleList() {
            const where = {}
            if (this.where.name != '') {
                where.name = this.where.name
            }
            this.loading = true
            roleList(where).then(res => {
                if (res.code == 200) {
                    this.list = res.data
                } else {
                    this.$message.error(res.data)
                }
            }).catch(e => {
                console.error(e)
                this.$message.error('获取列表时出现问题')
            }).finally(() => {
                this.loading = false
            })
        }
    }
}
</script>
<style scoped>
.container {
    width: 100%;
    min-height: 100vh;
    background-color: #efeff5;
    display: flex;
    flex-direction: column;
}
.search-container {
    display: flex;
    flex-direction: row;
    margin-top: 20px;
    padding: 0 20px;
}
.search-item {
    margin-right: 10px;
}
.table {
    margin-top: 20px;
}
.table-row {
    background-color: white;
}
</style>