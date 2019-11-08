<?php

namespace Demo\Http\Controllers\Admin;

use Demo\Model\Admin;
use Illuminate\Http\Request;
use Demo\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function lists(Request $request)
    {
        $roles = Role::select(['id','name','created_at','updated_at'])->paginate(intval($request->input('limit', 20)));
        return response()->json($roles);
    }

    public function selectRoles()
    {
        $roles = Role::select(['id', 'name'])->get();
        return response()->json($roles);
    }

    public function detail(Role $role)
    {
        $role['permissions'] = $role->permissions()->pluck('name');
        return response()->json($role->only(['id','name','permissions']));
    }

    public function create(Request $request)
    {
        $this->validate($request,[
            'name' => 'required|unique:roles|string',
            'permissions' => 'array',
        ], ['name.required'=>'名称不能为空','name.unique' => '角色已存在', 'permissions.array' => '权限必须为数组']);

        DB::beginTransaction();
        try {
            $role = Role::create(['guard_name'=>Admin::GUARD_NAME, 'name' => $request->input('name')]);
            $permissions = $request->input('permissions');
            $role->givePermissionTo($permissions);
            DB::commit();
            return response()->json(['msg'=>'添加成功']);
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();
        }
        return response()->json(['error'=>'添加失败,请稍后重试'], 400);
    }

    public function update(Role $role, Request $request)
    {
        if (!$role) {
            return response()->json(['msg' => '角色不存在'], 404);
        }
        $this->validate($request,[
            'name' => 'required|string',
            'permissions' => 'array',
        ], ['name.required'=>'名称不能为空', 'permissions.array' => '权限必须为数组']);

        DB::beginTransaction();
        try {
            $permissions = $request->input('permissions');
            $oldPermissions = $role->permissions()->pluck('name')->toArray();
            $commonPermissions = array_intersect($oldPermissions, $permissions);
            $del = array_diff($oldPermissions, $commonPermissions); //  删除的
            $add = array_diff($permissions, $commonPermissions); // 添加的
            var_dump($del);
            $role->revokePermissionTo($del);
            $role->givePermissionTo($add);
            $role->name = $request->input('name');
            $role->update();
            DB::commit();
            return response()->json(['msg'=>'更新成功']);
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();
        }
        return response()->json(['error'=>'更新失败,请稍后重试'], 400);
    }

    public function destroy(Role $role)
    {
        if ($role->users()->count() > 0) {
            return response()->json(['error' => '此角色下存在用户,不能删除'], 400);
        }
        try {
            $role->delete();
            return response()->json(['msg'=>'删除成功']);
        } catch (\Exception $e) {
            return response()->json(['error'=>'删除失败,请稍后重试'], 400);
        }

    }
}
