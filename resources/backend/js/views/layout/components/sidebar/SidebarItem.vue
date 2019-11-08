<template>
    <div class="menu-wrapper">
        <template v-for="item in routes" v-if="!item.hidden&&item.children&&$store.getters.hasPermission(item.permission)">
            <router-link v-if="item.children.length===1 && !item.children[0].children" :to="item.path+'/'+item.children[0].path" :key="item.children[0].name">
                <el-menu-item :index="item.path+'/'+item.children[0].path" :class="{'submenu-title-noDropdown':!isNest}">
                    <i v-if="item.children[0].meta&&item.children[0].meta.icon" :class="'fa fa-'+item.children[0].meta.icon"></i>
                    <span v-if="item.children[0].meta&&item.children[0].meta.title">{{generateTitle(item.children[0].meta.title)}}</span>
                </el-menu-item>
            </router-link>

            <el-submenu v-else :index="item.name||item.path" :key="item.name">
                <template slot="title">
                    <i v-if="item.meta&&item.meta.icon" :class="'fa fa-'+item.meta.icon"></i>
                    <span v-if="item.meta&&item.meta.title">{{generateTitle(item.meta.title)}}</span>
                </template>

                <template v-for="child in item.children" v-if="!child.hidden&&$store.getters.hasPermission(child.permission)">
                    <sidebar-item :is-nest="true" class="nest-menu" v-if="child.children&&child.children.length>0" :routes="[child]" :key="child.path"></sidebar-item>

                    <router-link v-else :to="item.path+'/'+child.path" :key="child.name">
                        <el-menu-item :index="item.path+'/'+child.path">
                            <i v-if="child.meta&&child.meta.icon" :class="'fa fa-'+child.meta.icon"></i>
                            <span v-if="child.meta&&child.meta.title">{{generateTitle(child.meta.title)}}</span>
                        </el-menu-item>
                    </router-link>
                </template>
            </el-submenu>

        </template>
    </div>
</template>

<script>
    import { generateTitle } from '@admin/utils/i18n'

    export default {
        name: 'SidebarItem',
        props: {
            routes: {
                type: Array
            },
            isNest: {
                type: Boolean,
                default: false
            }
        },
        methods: {
            generateTitle
        }
    }
</script>

