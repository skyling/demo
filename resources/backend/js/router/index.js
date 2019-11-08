import Vue from 'vue';
import VueRouter from 'vue-router';

Vue.use(VueRouter);

const _import = file => require('@admin/views/' + file + '.vue')

import Layout from '../views/layout/Layout.vue'

export const routerMap = [
    // name 与组件name 相同 用于keep-alive
    {path: '/login', component: _import('login/Index'), hidden: true},

    {
        path: '/system',
        component: Layout,
        redirect: '/system/role',
        permission: 'system-menu',
        name: 'system-menu',
        meta: {title: '系统设置', icon: 'gear'},
        children: [
            {
                path: 'role',
                component: _import('system/Role'),
                name: 'roles',
                permission: 'system-role-menu',
                meta: {title: '角色管理'}
            },
            {
                path: 'role/create',
                component: _import('system/RoleForm'),
                name: 'roleFormCreate',
                hidden: true,
                meta: {title: '添加角色'}
            },
            {
                path: 'role/:id/update',
                component: _import('system/RoleForm'),
                name: 'roleFormUpdate',
                hidden: true,
                meta: {title: '编辑角色'}
            },

            {
                path: 'account',
                component: _import('system/Account'),
                name: 'account',
                permission: 'system-account-menu',
                meta: {title: '账号管理'},
            },
            {
                path: 'account/create',
                component: _import('system/AccountForm'),
                name: 'accountFormCreate',
                hidden: true,
                meta: {title: '添加账号'}
            },
            {
                path: 'account/:id/update',
                component: _import('system/AccountForm'),
                name: 'accountFormUpdate',
                hidden: true,
                meta: {title: '编辑账号'}
            },
        ],
    },
    {
        path: '', component: Layout, redirect: '/dashboard',
        children: [
            {
                path: 'dashboard',
                component: _import('dashboard/Index'),
                name: 'dashboard',
                meta: {title: '录入基础数据', icon: 'dashboard', noCache: true}
            }
        ]
    },
]

export default new VueRouter({
    mode: 'hash',
    scrollBehavior: () => ({y: 0}),
    routes: routerMap,
})
