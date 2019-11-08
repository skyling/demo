import request from '@admin/utils/request'

// 上传参数
export function getUploadParams() {
    return request({url: '/uploadParams', method: 'get'})
}

export function refreshUploadParams() {
    return request({url: '/uploadParams?refresh=1', method: 'get'})
}

// 所有权限
export function getPermissions() {
    return request({url: '/permissions', method: 'get'})
}

// 角色
export function getRoles(query) {
    return request({url: '/roles', method: 'get', params: query})
}

export function getSelectRoles() {
    return request({url: '/role/selectRoles', method: 'get'})
}

export function getRole(id) {
    return request({url: '/role/' + id, method: 'get'})
}

export function createRole(data) {
    return request({url: '/role', method: 'post', data})
}

export function updateRole(id, data) {
    return request({url: '/role/' + id, method: 'put', data})
}

export function deleteRole(id) {
    return request({url: '/role/' + id, method: 'delete'})
}

// 账号
export function getAccounts(query) {
    return request({url: '/accounts', method: 'get', params: query})
}

export function getAccount(id) {
    return request({url: '/account/' + id, method: 'get'})
}

export function createAccount(data) {
    return request({url: '/account', method: 'post', data})
}

export function updateAccount(id, data) {
    return request({url: '/account/' + id, method: 'put', data})
}

export function deleteAccount(id) {
    return request({url: '/account/' + id, method: 'delete'})
}
