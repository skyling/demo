<?php

namespace Demo\Http\Controllers\Admin;

use Demo\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Util;

class AdminController extends Controller
{
    public function index()
    {
        return view('backend.home');
    }

    /**
     * 用户信息
     * @return \Illuminate\Http\JsonResponse
     */
    public function info()
    {
        $user = Auth::user();
        $data = $user->toArray();
        $data = array_only($data, ['id', 'email', 'username']);
        $data['permissions'] = $user->getPermissionsViaRoles()->pluck('name');
        return response()->json($data);
    }

    public function permissions()
    {
        return response()->json(config('permissionItems.admin'));
    }

    public function submitContent(Request $request)
    {
        Cache::forever('submit_content', $request->input());
        return response()->json(['msg' => '提交成功']);
    }
}
