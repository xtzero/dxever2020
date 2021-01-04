<template>
    <div class="container">
        <!-- 搜索 -->
        <div class="search-container">
            <div class="search-item">
                <a-input placeholder="按菜单名筛选" v-model="where.name"/>
            </div>
            <div class="search-item">
                <a-select style="width: 120px" v-model="where.type" allowClear placeholder="按类型筛选">
                    <a-select-option :value="type.key" v-for="(type, typeIndex) in typeOptions" :key="typeIndex">
                        {{ type.label }}
                    </a-select-option>
                </a-select>
            </div>
            <div class="search-item">
                <a-button type="primary" @click="getList">
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
            <a-table-column title="菜单名" data-index="name" />
            <a-table-column>
                <template #title>
                    类型
                </template>
                <template slot-scope="row">
                    {{ row.type == 'api' ? '接口' : '页面' }}
                </template>
            </a-table-column>
            <a-table-column title="路径" data-index="path" />
            <a-table-column>
                <template #title>
                    操作
                </template>
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
                        <a-button type="primary" @click="onSetRoleClick(row)">
                            设置角色
                            <a-icon type="more" />
                        </a-button>
                    </span>
                </template>
            </a-table-column>
        </a-table>
        <!-- 添加/编辑 -->
        <a-modal
            v-model="edit.display"
            :title="edit.id != 0 ? '编辑菜单' : '新建菜单'"
            ok-text="确认"
            cancel-text="取消"
            @ok="commitEdit"
            :ok-button-props="{ props: { loading: edit.loading} }"

        >
            <a-form :label-col="{ span: 5 }" :wrapper-col="{ span: 12 }">
                <a-form-item label="菜单名">
                    <a-input
                        v-model="edit.data.name"
                        placeholder="菜单名"
                    />
                </a-form-item>
                <a-form-item label="类型">
                    <a-select style="width: 120px" v-model="edit.data.type">
                        <a-select-option :value="type.key" v-for="(type, typeIndex) in typeOptions" :key="typeIndex">
                            {{ type.label }}
                        </a-select-option>
                    </a-select>
                </a-form-item>
                <a-form-item label="路径">
                    <a-input
                        v-model="edit.data.path"
                        placeholder="路径"
                    />
                </a-form-item>
            </a-form>
        </a-modal>

        <!-- 设置角色 -->
        <a-modal
            v-model="setRole.display"
            title="所属角色"
            ok-text="确认"
            cancel-text="取消"
            @ok="commitSetRole"
            :ok-button-props="{ props: { loading: setRole.loading} }"

        >
            <a-form :label-col="{ span: 5 }" :wrapper-col="{ span: 12 }">
                <a-form-item label="角色列表">
                    <a-select
                        mode="multiple"
                        v-model="setRole.data"
                        style="width: 100%"
                        :placeholder="setRole.loading ? '加载中' : '请选择角色'"
                    >
                        <a-select-option v-for="(role) in setRole.roles" :key="role.id" :value="role.id" :loading="setRole.loading">
                            {{ role.name }}
                        </a-select-option>
                    </a-select>
                </a-form-item>
            </a-form>
        </a-modal>
    </div>
</template>
<script>
import { menuList, saveMenu, deleteMenu, menuRoleList, roleList, saveMenuRole } from '@/api/auth'
export default {
    name: 'menus',
    data() {
        return {
            id: false,
            name: '',
            where: {
                type: '',
                name: ''
            },
            list: [],
            loading: false,
            typeOptions: [
                // {
                //     key: 'false',
                //     label: '全部',
                //     color: '#2db7f5'
                // },
                {
                    key: 'api',
                    label: '接口',
                    color: '#87d068'
                },
                {
                    key: 'page',
                    label: '页面',
                    color: '#108ee9'
                }
            ],
            currentType: 0,
            edit: {
                id: false,
                data: {
                    name: '',
                    path: '',
                    type: 'api'
                },
                display: false
            },
            setRole: {
                id: false,
                display: false,
                loading: false,
                data: [],
                roles: []
            }
        }
    },
    mounted() {
        this.getList()
    },
    methods: {
        commitSetRole() {
            this.setRole.loading = true
            saveMenuRole({
                id: this.setRole.id,
                'role_ids': this.setRole.data.join(',')
            }).then(res => {
                if (res.code == 200) {
                    this.$message.success('保存成功')
                    this.setRole.data = []
                    this.setRole.display = false
                } else {
                    this.$message.error(res.msg)
                }
            }).catch(e => {
                console.error(e)
                this.$message.error('保存角色菜单时出现问题')
            }).finally(() => {
                this.setRole.loading = false
            })
        },
        onRoleSelectChange(v) {
            console.log(v)
            this.setRole.data.push(v)
        },
        onSetRoleClick(row) {
            this.setRole.id = row.id
            this.setRole.display = true
            this.setRole.loading = true
            roleList({}).then(res => {
                if (res.code == 200) {
                    this.setRole.roles = res.data
                    menuRoleList({
                        id: row.id
                    }).then(res => {
                        if (res.code == 200) {
                            this.setRole.data = res.data.map(v => {return v.id})
                        } else {
                            this.$message.error(res.msg)
                        }
                    }).catch(e => {
                        console.error(e)
                        this.$message.error('获取角色列表时出现问题')
                    }).finally(() => {
                        this.setRole.loading = false
                    })
                } else {
                    this.$message.error(res.msg)
                }
            }).catch(e => {
                console.error(e)
                this.$message.error('获取角色列表时出现问题')
            })
        },
        onDeleteClick(row) {
            this.$confirm({
                content: `确定要删除菜单 ${row.name} 吗？`,
                onOk:() => {
                    this.loading = true
                    deleteMenu({
                        id: row.id
                    }).then(res => {
                        if (res.code == 200) {
                            this.$message.success('删除成功')
                            this.getList()
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
        onEditClick(row) {
            this.edit.id = row.id
            this.edit.data = {
                name: row.name,
                path: row.path,
                type: row.type
            }
            this.edit.display = true
        },
        onAddClick() {
            this.edit.id = false
            this.edit.data = {
                name: '',
                path: '',
                type: 'api'
            }
            this.edit.display = true
        },
        commitEdit() {
            const saveArr = {}
            if (this.edit.id) {
                saveArr.id = this.edit.id
            }
            if (this.edit.data.name == '') {
                this.$message.error('请输入菜单名')
                return
            } else {
                saveArr.name = this.edit.data.name
            }
            if (this.edit.data.path == '') {
                this.$message.error('请输入菜单路径')
                return
            } else {
                saveArr.path = this.edit.data.path
            }
            if (this.edit.data.type == '') {
                this.$message.error('请选择菜单类型')
                return
            } else {
                saveArr.type = this.edit.data.type
            }
            this.edit.loading = true
            saveMenu(saveArr).then(res => {
                if (res.code == 200) {
                    this.$message.success('保存成功')
                    this.edit.display = false
                    this.getList()
                } else {
                    this.$message.error(res.msg)
                }
            }).catch(e => {
                console.error(e)
                this.$message.error('保存菜单时出现问题')
            }).finally(() => {
                this.edit.loading = false
            })
        },
        getList() {
            this.loading = true
            const where = {}
            if (this.where.type != '') {
                where.type = this.where.type
            }
            if (this.where.name != '') {
                where.name = this.where.name
            }
            menuList(where).then(res => {
                if (res.code == 200) {
                    this.list = res.data
                } else {
                    this.$message.error(res.msg)
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
<style>
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