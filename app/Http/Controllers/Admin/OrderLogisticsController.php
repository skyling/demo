<?php

namespace Demo\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Demo\Http\Controllers\Controller;
use Demo\Model\Export;
use Demo\Model\OrderLogistics;
use Demo\Services\MatchService;
use Demo\Services\SettlementService;
use Demo\Util\Helper;

class OrderLogisticsController extends Controller
{
    public function lists(Request $request)
    {
        $data = OrderLogistics::query()
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
        $sum = OrderLogistics::query()
            ->listWhere($request)
            ->select(DB::raw('sum(money) money, sum(weight) weight'))->first();
        $data = Helper::apiPaginate($data);
        $sum['money'] = round($sum['money'], 2);
        $data['summary'] = $sum;
        return $data;
    }

    /**
     * 订单物流匹配
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function match(Request $request)
    {
        $ret = MatchService::logisticsMatch($request);
        if (count($ret)) {
            return response()->json(['msg' => '匹配成功条数:' . count($ret)]);
        }
        return response()->json(['error' => '未匹配到任何数据'], 400);
    }

    public function detail(OrderLogistics $orderLogistics)
    {
        $orderLogistics->load(['shop', 'order']);
        return $orderLogistics;
    }

    public function update(OrderLogistics $orderLogistics, Request $request)
    {
        if ($orderLogistics->settlement_id) {
            return response()->json(['error' => '已结算不可编辑'], 400);
        }
        $orderLogistics->fill($request->only(['money', 'weight', 'shipping_at']));
        $orderLogistics->logistics_id = -1;
        if ($orderLogistics->save()) {
            SettlementService::orderProfile($orderLogistics->order_id);
            return response()->json(['msg' => '修改成功']);
        }
        return response()->json(['error' => '修改失败,请稍后重试'], 400);
    }

    public function destroy(OrderLogistics $orderLogistics)
    {
        if ($orderLogistics->settlement_id) {
            return response()->json(['error' => '已结算不可删除'], 400);
        }
        if ($orderLogistics->delete()) {
            SettlementService::orderProfile($orderLogistics->order_id);
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
        $export = Export::saveExport(Export::TYPE_ORDER_LOGISTICS, $request);
        return response()->json(['msg' => '导出任务新建成功,任务ID为:' . $export->id]);
    }
}
