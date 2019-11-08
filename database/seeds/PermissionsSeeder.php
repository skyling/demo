<?php

use Illuminate\Database\Seeder;
use \Spatie\Permission\Models\Permission;
use \Spatie\Permission\Models\Role;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 权限控制
     * @return void
     */
    public function run()
    {
        app()['cache']->forget('spatie.permission.cache');

        $permissions = config('permissionItems');
        foreach($permissions as $guardName=>$permission) {
            $guardName = is_numeric($guardName) ? null : $guardName;
            $role = $guardName ? $this->findOrCreateRole($guardName) : null;
            $this->createPermission($permission, $guardName, $role);
        }
        $admin = \Demo\Model\Admin::where(['username' => '超级管理员'])->first();
        if ($admin) {
            try{
                $admin->assignRole('admin');
            } catch(\Exception $e){
                \Illuminate\Support\Facades\Log::error($e);
            }

        }
        echo "permission created done!\r\n";
    }

    /**
     * 创建一个角色
     * @param null $guardName
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    protected function findOrCreateRole($guardName=null)
    {
        $role = Role::where(['guard_name'=>$guardName, 'name'=>$guardName])->first();
        if (!$role) {
            $role = Role::create(['guard_name'=>$guardName, 'name'=>$guardName]);
        }
        return $role;
    }

    /**
     * 创建权限
     * @param $permissions
     * @param null $guard_name
     */
    protected function createPermission($permissions, $guard_name = null,Role $role=null)
    {
        if (!isset($permissions['name'])) {
            foreach($permissions as $subPermission) {
                $this->createPermission($subPermission, $guard_name, $role);
            }
        } else {
            $name = $permissions['name'];
            try {
                Permission::create(compact('name', 'guard_name'));
                if($role) $role->givePermissionTo($name);
            } catch(\Exception $e){
                \Illuminate\Support\Facades\Log::error($e);
            }
            if(array_has($permissions, 'subs')) {
                $this->createPermission($permissions['subs'], $guard_name, $role);
            }
        }
    }
}
