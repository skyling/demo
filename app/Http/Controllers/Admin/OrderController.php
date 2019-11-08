<?php

namespace Demo\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Demo\Http\Controllers\Controller;
use Demo\Model\Export;
use Demo\Model\Import;
use Demo\Model\Order;
use Demo\Services\SettlementService;
use Demo\Util\Helper;

class OrderController extends Controller
{
    /**
     * 订单列表
     * @param Request $request
     */
    public function lists(Request $request)
    {
        $data = Order::query()
            ->with([
                'shop' => function ($query) {
                    $query->select('id', 'name');
                },
                'goods' => function ($query) {
                    $query->select('*');
                },
                'logistics' => function ($query) {
                    $query->select('*');
                },
                'refund' => function ($query) {
                    $query->select('*');
                }
            ])
            ->listWhere($request)
            ->paginate($request->input('limit', 20));
        $sum = Order::query()
            ->listWhere($request)
            ->select(DB::raw('sum(money) money, sum(origin_money) origin_money'))->first();
        $data = Helper::apiPaginate($data);
        $sum['money'] = round($sum['money'], 2);
        $sum['origin_money'] = round($sum['origin_money'], 2);
        $data['summary'] = $sum;
        return $data;
    }

    public function detail(Order $order)
    {
        $order->load(['shop']);
        return $order;
    }

    public function create(Request $request)
    {

    }

    /**
     * 更新订单
     * @param Order $order
     * @param Request $request
     */
    public function update(Order $order, Request $request)
    {
        if ($order->settlement_id) {
            return response()->json(['error' => '已结算不可编辑'], 400);
        }
        $order->fill($request->only(['money', 'origin_money', 'shipping_at', 'order_at', 'pay_at']));
        if ($order->save()) {
            SettlementService::orderProfile($order->id);
            return response()->json(['msg' => '修改成功']);
        }
        return response()->json(['error' => '修改失败,请稍后重试'], 400);
    }

    /**
     * 删除订单
     * @param Order $order
     */
    public function destroy(Order $order)
    {
        if ($order->settlement_id) {
            return response()->json(['error' => '已结算订单不可删除'], 400);
        }
        DB::beginTransaction();
        try {
            $order->logistics()->delete();
            $order->goods()->delete();
            $order->refund()->delete();
            $order->delete();
            DB::commit();
            return response()->json(['msg' => '删除成功']);
        } catch (\Exception $e) {
            DB::rollBack();
        }
        return response()->json(['error' => '删除失败,请稍后重试'], 400);
    }

    /**
     * 导入订单
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function import(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|string',
            'name' => 'required|string',
        ]);
        $import = new Import();
        $import->fill([
            'admin_user_id' => Auth::user()->id,
            'filepath' => $request->input('file'),
            'name' => $request->input('name'),
            'type' => Import::TYPE_ORDER
        ]);
        $import->save();
        return response()->json(['msg' => '导入成功,任务ID为:' . $import->id]);
    }

    /**
     * 导出
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function export(Request $request)
    {
        $export = Export::saveExport(Export::TYPE_ORDER, $request);
        return response()->json(['msg' => '导出任务新建成功,任务ID为:' . $export->id]);
    }
}
