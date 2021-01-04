<template>
    <div class="container">
        <!-- 搜索 -->
        <div class="search-container">
            <div class="search-item">
                <a-input placeholder="使用用户名字模糊搜索" v-model="where.name"/>
            </div>
            <div class="search-item">
                <a-input placeholder="使用用户名模糊搜索" v-model="where.username"/>
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
            <a-table-column title="uid" data-index="uid" />
            <a-table-column title="用户名" data-index="username" />
            <a-table-column title="名字" data-index="name" />
            <a-table-column title="创建时间" data-index="create_time" />
            <a-table-column title="修改时间" data-index="modify_time" />
            <a-table-column title="角色" >
                <template slot-scope="row">
                    <a-tag color="#108ee9" v-for="(role, roleIndex) in row.roles" :key="roleIndex">
                        {{ role.role_name }}
                    </a-tag>
                </template>
            </a-table-column>
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
            :title="edit.uid != 0 ? '编辑系统用户' : '新增系统用户'"
            ok-text="确认"
            cancel-text="取消"
            @ok="commitEdit"
            :ok-button-props="{ props: { loading: edit.loading} }"

        >
            <a-form :label-col="{ span: 5 }" :wrapper-col="{ span: 12 }">
                <a-form-item label="用户名字">
                    <a-input
                        v-model="edit.data.name"
                        placeholder="用户名字"
                    />
                </a-form-item>
                <a-form-item label="用户名">
                    <a-input
                        v-model="edit.data.username"
                        placeholder="用户名"
                    />
                </a-form-item>
                <a-form-item label="登录密码">
                    <a-input
                        v-model="edit.data.password"
                        placeholder="登录密码"
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
import { sysUserList, saveSysUser, deleteSysUser, roleList, setUserRole } from '@/api/auth'
export default {
    name: 'role',
    data() {return {
        list: [],
        loading: false,
        where: {
            name: '',
            username: ''
        },
        edit: {
            display: false,
            uid: false,
            data: {
                name: '',
                username: '',
                password: ''
            },
            loading: false
        },
        setRole: {
            uid: false,
            display: false,
            loading: false,
            data: [],
            roles: []
        }
    }},
    mounted() {
        this.getList()
    },
    methods: {
        commitSetRole() {
            this.setRole.loading = true
            setUserRole({
                uid: this.setRole.uid,
                'role_ids': this.setRole.data.join(',')
            }).then(res => {
                if (res.code == 200) {
                    this.$message.success('保存成功')
                    this.setRole.data = []
                    this.setRole.display = false
                    this.getList()
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
            this.setRole.uid = row.uid
            this.setRole.display = true
            this.setRole.loading = true
            this.setRole.data = []
            roleList({}).then(res => {
                if (res.code == 200) {
                    this.setRole.roles = res.data
                    this.setRole.data = row.roles.map(v => {return v.role_id})
                } else {
                    this.$message.error(res.msg)
                }
            }).catch(e => {
                console.error(e)
                this.$message.error('获取角色列表时出现问题')
            }).finally(() => {
                this.setRole.loading = false
            })
        },
        onAddClick() {
            this.edit.data = Object.assign({}, this.edit.oriData)
            this.edit.uid = false
            this.edit.display = true
        },
        commitEdit() {
            const saveData = {}
            saveData.uid = this.edit.uid
            if (this.edit.data.name != '') {
                saveData.name = this.edit.data.name
            } else {
                this.$message.error('请输入用户名字')
                return
            }
            if (this.edit.data.username != '') {
                saveData.username = this.edit.data.username
            } else {
                this.$message.error('请输入用户名')
                return
            }
            if (this.edit.data.password != '') {
                saveData.password = this.edit.data.password
            } else {
                this.$message.error('请输入用户密码')
                return
            }
            
            this.edit.loading = true
            saveSysUser(saveData).then(res => {
                if (res.code == 200) {
                    this.$message.success('保存成功')
                    this.edit.display = false
                    this.getList()
                } else {
                    this.$message.error(res.msg)
                }
            }).catch(e => {
                console.error(e)
                this.$message.error('保存用户信息时出现问题')
            }).finally(() => {
                this.edit.loading = false
            })
        },
        onEditClick(row) {
            this.edit.uid = row.uid
            this.edit.data.name = row.name
            this.edit.data.username = row.username
            this.edit.data.password = row.password
            this.edit.display = true
        },
        onDeleteClick(row) {
            this.$confirm({
                content: `确定要删除用户 ${row.name} 吗？`,
                onOk:() => {
                    this.loading = true
                    deleteSysUser({
                        uid: row.uid
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
        getList() {
            const where = {}
            if (this.where.name != '') {
                where.name = this.where.name
            }
            this.loading = true
            sysUserList(where).then(res => {
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