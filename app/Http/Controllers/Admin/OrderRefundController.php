<?php

namespace Demo\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Demo\Http\Controllers\Controller;
use Demo\Model\Export;
use Demo\Model\OrderRefund;
use Demo\Services\SettlementService;
use Demo\Util\Helper;

class OrderRefundController extends Controller
{
    public function lists(Request $request)
    {
        $data = OrderRefund::query()
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
        $sum = OrderRefund::query()
            ->listWhere($request)
            ->select(DB::raw('sum(money) money'))->first();
        $data = Helper::apiPaginate($data);
        $sum['money'] = round($sum['money'], 2);
        $data['summary'] = $sum;
        return $data;
    }

    public function detail(OrderRefund $orderRefund)
    {
        $orderRefund->load(['shop', 'order']);
        return $orderRefund;
    }

    public function update(OrderRefund $orderRefund, Request $request)
    {
        if ($orderRefund->settlement_id) {
            return response()->json(['error' => '已结算不可编辑'], 400);
        }
        $orderRefund->fill($request->only(['money', 'weight', 'refund_at']));
        if ($orderRefund->save()) {
            SettlementService::orderProfile($orderRefund->order_id);
            return response()->json(['msg' => '修改成功']);
        }
        return response()->json(['error' => '修改失败,请稍后重试'], 400);
    }

    /**
     * 删除
     * @param OrderRefund $orderRefund
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(OrderRefund $orderRefund)
    {
        if ($orderRefund->settlement_id) {
            return response()->json(['error' => '已结算不可删除'], 400);
        }
        if ($orderRefund->delete()) {
            SettlementService::orderProfile($orderRefund->order_id);
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
        $export = Export::saveExport(Export::TYPE_ORDER_REFUND, $request);
        return response()->json(['msg' => '导出任务新建成功,任务ID为:' . $export->id]);
    }
}
