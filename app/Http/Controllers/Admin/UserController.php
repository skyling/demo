<?php

namespace Demo\Http\Controllers\Admin;

use Demo\User;
use Demo\Util\Helper;
use Illuminate\Http\Request;
use Demo\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class UserController extends Controller
{
    public function lists(Request $request)
    {
        return User::query()->select('id,shop_name,dxm_shop_id')->paginate(intval($request->input('limit', 20)));
    }
}
