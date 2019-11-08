<?php

namespace Demo\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Demo\Http\Controllers\Controller;
use Demo\Model\Settlement;
use Demo\Services\SettlementService;
use Demo\Util\Helper;

class SettlementController extends Controller
{
    public function lists(Request $request)
    {
        $data = Settlement::query()
            ->with(['settlementItems', 'settlementer' => function ($query) {
                $query->select('id', 'username');
            }, 'shop' => function ($query) {
                $query->select('id', 'name');
            }, 'admin' => function ($query) {
                $query->select('id', 'username');
            }])
            ->listWhere($request)
            ->orderBy('id', 'desc')
            ->paginate($request->input('limit', 20));
        return Helper::apiPaginate($data);
    }

    public function items(Request $request)
    {
        $this->validate($request, [
            'shop_id' => 'required|exists:shops,id',
            'month' => 'required|date',
            'date_start' => 'date',
        ], [
            'shop_id.required' => '请选择店铺',
            'month.required' => '请选择月份', 'month.date' => '月份格式不正确',
        ]);
        $items = SettlementService::items($request->input('shop_id'), $request->input('month'),
            $request->input('date_start'));
        $data['items'] = array_values($items);
        $data['total'] = array_sum(array_column($data['items'], 'money'));
        return response()->json($data);
    }

    /**
     * 确认结算
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function confirm(Request $request)
    {
        $this->validate($request, [
            'shop_id' => 'required|exists:shops,id',
            'month' => 'required|date',
            'date_start' => 'nullable|date',
            'remark' => 'string|max:100',
        ], [
            'shop_id.required' => '请选择店铺',
            'month.required' => '请选择月份', 'month.date' => '月份格式不正确',
            'remark.max' => '结算备注不能超过100字'
        ]);
        $ret = SettlementService::confirm($request->input('shop_id'), $request->input('month'),
            $request->input('date_start'), $request->input('remark'));
        if ($ret) {
            return response()->json(['msg' => '结算成功']);
        }
        return response()->json(['error' => '结算失败'], 400);
    }

    /**
     * 返款
     * @param Settlement $settlement
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function settlementReturn(Settlement $settlement, Request $request)
    {
        if ($settlement->settlement_at) {
            return response()->json(['error' => '已返款'], 400);
        }
        $this->validate($request, [
            'settlement_money' => 'required',
            'settlement_at' => 'required|date',
            'settlement_remark' => 'string|max:100',
        ], [
            'settlement_money.required' => '请输入返款金额',
            'settlement_at.required' => '请选择返款时间', 'settlement_at.date' => '返款时间格式不正确',
            'settlement_remark.max' => '结算备注不能超过100字'
        ]);
        $data = $request->only(['settlement_money', 'settlement_at', 'settlement_remark']);
        $data['settlement_user_id'] = Auth::user()->id;
        $settlement->fill($data);
        if ($settlement->save()) {
            return response()->json(['msg' => '操作成功']);
        }
        return response()->json(['error' => '操作失败,请稍后重试'], 400);
    }
}
