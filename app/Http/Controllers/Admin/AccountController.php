<?php

namespace Demo\Http\Controllers\Admin;

use Demo\Model\Admin;
use Illuminate\Http\Request;
use Demo\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Demo\Util\Helper;
use Illuminate\Support\Facades\Log;

class AccountController extends Controller
{
    public function lists(Request $request)
    {
        $lists = Admin::query()->paginate(intval($request->input('limit', 20)));
        return response()->json($lists);
    }

    public function detail(Admin $account)
    {
        $account['role'] = $account->roles()->pluck('name')->first();
        return response()->json($account);
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|string',
            'email' => 'required|email|unique:admins',
            'password' => 'required|string',
            'status' => 'required|in:1,2',
            'role' => 'required|exists:roles,name,guard_name,' . Admin::GUARD_NAME
        ], [
            'username.required' => '用户名不能为空',
            'email.required' => '邮箱不能为空', 'email.email' => '邮箱格式不正确', 'email.unique' => '邮箱已存在',
            'password.required' => '密码不能为空',
            'status.required' => '状态不能为空', 'status.in' => '状态不正确',
            'role.required' => '角色不正确', 'role.exists' => '角色不存在',
        ]);

        $admin = new Admin();
        $admin->fill($request->only(['username', 'email', 'password', 'status']));
        if ($admin->save()) {
            $admin->assignRole($request->only('role'));
            return response()->json(['msg' => '添加成功']);
        }
        return response()->json(['error' => '添加失败'], 400);
    }

    public function update(Admin $account, Request $request)
    {
        if (!$account) {
            return response()->json(['账号不存在'], 400);
        }
        $this->validate($request, [
            'username' => 'sometimes|string',
            'email' => 'sometimes|email',
            'password' => 'sometimes|string',
            'role' => 'sometimes|string',
            'status' => 'sometimes|in:0,1',
            'shop_id' => 'sometimes|array'
        ], ['email.email' => '邮箱格式不正确', 'status.in' => '状态不正确']);
        $data = Helper::arrayFilter($request->only(['username', 'email', 'status', 'password', 'shop_id']));
        if ($request->input('role')) {
            $account->syncRoles($request->input('role'));
        }
        if ($account->fill($data)->update()) {
            return response()->json(['msg' => '修改成功']);
        }
        return response()->json(['error' => '修改失败,请稍后重试'], 400);

    }

    public function destroy(Admin $account)
    {
        try {
            $account->delete();
            return response()->json(['msg' => '删除成功']);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['msg' => '删除失败, 请稍后重试'], 400);
        }
    }

}
