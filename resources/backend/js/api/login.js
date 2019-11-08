import request from '@admin/utils/request'

export function login(email, password) {
  const data = {email, password}
  return request({url: '/login', method: 'post', data})
}

export function refreshToken() {
  return request({url:'refreshToken', method:'get',})
}

export function logout() {
  return request({url: '/logout', method: 'get'})
}

export function getUserInfo() {
  return request({url: '/info', method: 'get',})
}