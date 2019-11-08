<?php

namespace Demo\Http\Controllers\Admin;

use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Demo\Http\Controllers\Controller;
use Demo\Model\Export;
use Demo\Model\OrderGoods;
use Demo\Services\MatchService;
use Demo\Services\SettlementService;
use Demo\Util\Helper;

class OrderGoodsController extends Controller
{
    public function lists(Request $request)
    {
        $data = OrderGoods::query()
            ->with([
                'shop' => function ($query) {
                    $query->select('id', 'name');
                },
                'order' => function ($query) {
                    $query->select('id', 'order_no', 'shipping_at', 'money');
                }
            ])
            ->listWhere($request)
            ->paginate($request->input('limit', 20));
        $sum = OrderGoods::query()
            ->listWhere($request)
            ->select(DB::raw('sum(money) money, sum(count) count'))->first();
        $data = Helper::apiPaginate($data);
        $sum['money'] = round($sum['money'], 2);
        $data['summary'] = $sum;
        return $data;
    }

    /**
     * 订单商品匹配
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function match(Request $request)
    {
        $ret = MatchService::goodsMatch($request);
        if (count($ret)) {
            return response()->json(['msg' => '匹配成功条数:' . count($ret)]);
        }
        return response()->json(['error' => '未匹配到任何数据'], 400);
    }

    public function detail(OrderGoods $orderGoods)
    {
        $orderGoods->load(['shop', 'order']);
        return $orderGoods;
    }

    public function update(OrderGoods $orderGoods, Request $request)
    {
        if ($orderGoods->settlement_id) {
            return response()->json(['error' => '已结算不可编辑'], 400);
        }
        $orderGoods->fill($request->only(['money', 'count', 'shipping_at']));
        $orderGoods->purchases_id = -1;
        if ($orderGoods->save()) {
            SettlementService::orderProfile($orderGoods->order_id);
            return response()->json(['msg' => '修改成功']);
        }
        return response()->json(['error' => '修改失败,请稍后重试'], 400);
    }

    /**
     * 删除商品
     * @param OrderGoods $orderGoods
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(OrderGoods $orderGoods)
    {
        if ($orderGoods->settlement_id) {
            return response()->json(['error' => '已结算不可删除'], 400);
        }
        if ($orderGoods->delete()) {
            SettlementService::orderProfile($orderGoods->order_id);
            return response()->json(['msg' => '删除成功']);
        }
        return response()->json(['error' => '删除失败,请稍后重试']);
    }

    /**
     * 导出
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function export(Request $request)
    {
        $export = Export::saveExport(Export::TYPE_ORDER_GOODS, $request);
        return response()->json(['msg' => '导出任务新建成功,任务ID为:' . $export->id]);
    }
}
