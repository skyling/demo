<?php

namespace Demo\Http\Controllers\Admin;

use Demo\Model\AdminLoginLog;
use Demo\Model\Admin;
use Illuminate\Http\Request;
use Demo\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * 用户登录
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|string',
            'password' => 'required|string',
        ], ['email.required' => '用户名不能为空','email.email' => '用户名必须是邮箱', 'password.required' => '密码不能为空']);
        $data  = $request->only(['email', 'password']);
        $admin = Admin::query()->where(['email'=>$request->input('email')])->first();
        if ($admin && $admin->status == 0) {
            return response()->json(['error' => '用户已禁用'], 401);
        }
        if (($token = $this->guard()->attempt($data))) {
            // 登录日志
            AdminLoginLog::log($admin->id, $request->getClientIp());
            return $this->respondWithToken($token);
        }
        return response()->json(['error' => '登录失败,用户名或密码错误'], 401);
    }

    /**
     * 退出登录
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        try{
            $this->guard()->logout();
        } catch(\Exception $e){}
        return response()->json(['msg' => '退出登录成功']);
    }

    /**
     * 刷新token
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken($this->guard()->refresh());
    }

    /**
     * 返回数据
     * @param $token
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60
        ]);
    }

    public function guard()
    {
        return Auth::guard('admin');
    }

}
