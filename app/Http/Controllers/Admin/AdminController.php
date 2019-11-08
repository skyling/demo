<?php

namespace Demo\Http\Controllers\Admin;

use Demo\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
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
}
