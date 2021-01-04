<template>
    <div class="container">
        <!-- 搜索 -->
        <a-page-header
            :title="`设置「${name}」的菜单`"
            @back="() => {$router.back()}"
            :ghost="false"
            style="width: 100%"
        >
            <template #extra>
                <a-button type="primary" @click="commitSave">
                    <a-icon type="check" />
                    保存
                </a-button>
            </template>
        </a-page-header>
        <!-- 表格 -->
        <a-table :rowClassName="()=>'table-row'" class="table" :data-source="list" :loading="loading" :pagination="false" :rowKey="(record)=>record.id">
            <a-table-column title="序号" :customRender="(t, r, i) => i + 1" />
            <a-table-column title="菜单名" data-index="name" />
            <a-table-column>
                <template #title>
                    类型
                    <a-tag :color="typeOptions[currentType].color" @click="changeType">
                        {{ typeOptions[currentType].label }}
                        <a-icon type="swap" />
                    </a-tag>
                </template>
                <template slot-scope="row">
                    {{ row.type == 'api' ? '接口' : '页面' }}
                </template>
            </a-table-column>
            <a-table-column title="路径" data-index="path" />
            <a-table-column>
                <template #title>
                    选定
                    <a @click="selectAll">全选</a>
                    /
                    <a @click="unSelectAll">全不选</a>
                </template>
                <template slot-scope="row">
                    <span>
                        <a-checkbox :checked="row.have == 1 ? true : false" @change="(e) => {row.have = e.target.checked ? 1 : 0}"/>
                    </span>
                </template>
            </a-table-column>
        </a-table>
    </div>
</template>
<script>
import { getRoleMenuList, saveRoleMenu } from '@/api/auth'
export default {
    name: 'roleMenu',
    data() {
        return {
            id: false,
            name: '',
            where: {
                type: false
            },
            list: [],
            loading: false,
            typeOptions: [
                {
                    key: false,
                    label: '全部',
                    color: '#2db7f5'
                },
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
            currentType: 0
        }
    },
    created() {
        const id = this.$route.query.id
        if (!id) {
            this.$router.back()
        } else {
            this.id = id
            this.name = this.$route.query.name
        }
    },
    mounted() {
        this.getRoleMenus()
    },
    methods: {
        unSelectAll() {
            this.list.forEach(v => {
                v.have = 0
            })
        },
        selectAll() {
            this.list.forEach(v => {
                v.have = 1
            })
        },
        changeType() {
            this.currentType == 2 ? this.currentType = 0 : this.currentType ++
            this.where.type = this.typeOptions[this.currentType].key
            this.getRoleMenus()
        },
        commitSave() {
            const idArr = []
            this.list.forEach(v => {
                if (v.have == 1) {
                    idArr.push(v.id)
                }
            })
            this.loading = true
            saveRoleMenu({
                id: this.id,
                'menu_ids': idArr.length > 0 ? idArr.join(',') : ''
            }).then(res => {
                if (res.code == 200) {
                    this.$message.success('保存成功')
                    this.$router.back()
                } else {
                    this.$message.error(res.msg)
                }
            }).catch(e => {
                console.error(e)
                this.$message.error('保存菜单时出现问题')
            }).finally(() => {
                this.loading = false
            })
        },
        getRoleMenus() {
            this.loading = true
            const where = {
                id: this.id
            }
            if (this.where.type) {
                where.type = this.where.type
            }
            getRoleMenuList(where).then(res => {
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