<?php
/**
 * 系统权限管理
 * Author: lifuren <frenlee@163.com>
 * Since: 2018/1/6 23:09
 */
return [
    // guard_name
    'admin' => [
        [
            'title' => '系统设置',
            'name' => 'system-menu',
            'subs' => [
                [
                    'title' => '角色管理',
                    'name' => 'system-role-menu',
                    'subs' => [
                        ['title' => '角色列表', 'name' => 'role-list'],
                        ['title' => '编辑角色', 'name' => 'role-update'],
                        ['title' => '添加角色', 'name' => 'role-create'],
                        ['title' => '删除角色', 'name' => 'role-delete'],
                    ],
                ],
                [
                    'title' => '账号管理',
                    'name' => 'system-account-menu',
                    'subs' => [
                        ['title' => '账号列表', 'name' => 'account-list'],
                        ['title' => '添加账号', 'name' => 'account-create'],
                        ['title' => '修改账号', 'name' => 'account-update'],
                        ['title' => '删除账号', 'name' => 'account-delete'],
                    ]
                ]
            ]
        ],
    ]
];
