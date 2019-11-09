<?php

namespace Demo\Http\Controllers;

use Demo\Model\Goods;
use Demo\Model\GoodsAttribute;
use Demo\Model\GoodsCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Cache::get('submit_content');
        return view('home', compact('data'));
    }

}
