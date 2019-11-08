<?php

namespace Demo\Http\Controllers\Admin;

use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Demo\Http\Controllers\Controller;
use Demo\Model\Shop;
use Demo\Util\Helper;

class ShopController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function lists(Request $request)
    {
        $data = Shop::query()->paginate($request->input('limit', 20));
        return response()->json($data);
    }

    /**
     * 店铺选项
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function options()
    {
        $data = Shop::query()->select('id', 'name')->get();
        return response()->json($data);
    }

    /**
     * 店铺详细
     * @param Shop $shop
     * @return \Illuminate\Http\JsonResponse
     */
    public function detail(Shop $shop)
    {
        $data = $shop->only(['id', 'name']);
        return response()->json($data);
    }

    /**
     * 创建
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $this->validate($request,[
            'name' => 'required|unique:shops|string',
        ], ['name.required'=>'名称不能为空']);
        $ret = Shop::query()->create($request->only(['name']));
        if ($ret) {
            return response()->json(['msg' => '添加成功']);
        }
        return response()->json(['error' => '添加失败,请稍后重试'], 400);
    }

    /**
     * 更新
     * @param Shop $shop
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Shop $shop, Request $request)
    {
        if (!$shop) {
            return response()->json(['店铺不存在'], 400);
        }
        $this->validate($request, [
            'name' => 'required|unique:shops|string',
        ],['name.required' => '名称不能为空']);
        $data = Helper::arrayFilter($request->only(['name']));
        if($shop->fill($data)->update()) {
            return response()->json(['msg'=>'修改成功']);
        }
        return response()->json(['error' => '修改失败,请稍后重试'], 400);
    }

    /**
     * 删除
     * @param Shop $shop
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Shop $shop)
    {
        try {
            $shop->delete();
            return response()->json(['msg' => '删除成功']);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['msg' => '删除失败, 请稍后重试'], 400);
        }
    }
}
