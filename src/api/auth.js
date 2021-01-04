import { request } from '@/utils/request'
// 用户
export function sysUserList(data) {
    return request('GET', 'sys/sysUserList', data)
}
export function saveSysUser(data) {
    return request('POST', 'sys/saveSysUser', data)
}
export function deleteSysUser(data) {
    return request('POST', 'sys/deleteSysUser', data)
}
export function setUserRole(data) {
    return request('POST', 'sys/setUserRole', data)
}

// 角色
export function roleList(data) {
    return request('GET', 'sys/roleList', data)
}
export function saveRole(data) {
    return request('POST', 'sys/saveRole', data)
}
export function deleteRole(data) {
    return request('POST', 'sys/deleteRole', data)
}
export function getRoleMenuList(data) {
    return request('GET', 'sys/getRoleMenuList', data)
}
export function saveRoleMenu(data) {
    return request('POST', 'sys/saveRoleMenu', data)
}

// 菜单
export function menuList(data) {
    return request('GET', 'sys/menuList', data)
}
export function menuRoleList(data) {
    return request('GET', 'sys/menuRoleList', data)
}
export function saveMenu(data) {
    return request('POST', 'sys/saveMenu', data)
}
export function deleteMenu(data) {
    return request('POST', 'sys/deleteMenu', data)
}
export function saveMenuRole(data) {
    return request('POST', 'sys/saveMenuRole', data)
}