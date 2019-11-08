const getters = {
    sidebar: state => state.app.sidebar,
    language: state => state.app.language,
    visitedViews: state => state.tagsView.visitedViews,
    cachedViews: state => state.tagsView.cachedViews,
    token: state => state.user.token,
    name: state => state.user.name,
    permissions: state => state.user.permissions,
    permission_routers: state => state.permission.routers,
    errorLogs: state => state.errorLog.logs,
    hasPermission: (state) => (permission) => {return !permission || state.user.permissions.indexOf(permission) >=0;}, // 权限管理 未设置就表示通过
    uploadParams: state => state.app.uploadParams,
}
export default getters
