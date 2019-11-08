<?php

namespace Demo\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Demo\Http\Controllers\Controller;
use Demo\Model\OtherFee;
use Demo\Util\Helper;

class OtherFeeController extends Controller
{
    public function lists(Request $request)
    {
        $data = OtherFee::query()
            ->with([
                'shop' => function ($query) {
                    $query->select('id', 'name');
                }
            ])
            ->listWhere($request)
            ->orderBy('id', 'desc')
            ->paginate($request->input('limit', 20));
        $data = Helper::apiPaginate($data);
        return $data;
    }

    public function detail(OtherFee $otherFee)
    {
        return response()->json($otherFee);
    }

    /**
     * 新增
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'shop_id' => 'required|exists:shops,id',
            'pay_at' => 'required|date',
            'money' => 'required|numeric',
            'remark' => 'required|string',
        ], [
            'shop_id.required' => '请选择店铺',
            'pay_at.required' => '支出时间不能为空', 'pay_at.date' => '支出时间格式不对',
            'money.required' => '请输入金额', 'money.numeric' => '金额必须为数字',
            'remark.required' => '备注不能为空'
        ]);

        $otherFee = new OtherFee();
        $otherFee->fill($request->only(['shop_id', 'pay_at', 'money', 'remark']));
        if ($otherFee->save()) {
            return response()->json(['msg' => '添加成功']);
        }
        return response()->json(['error' => '添加失败'], 400);
    }

    /**
     * 修改
     * @param OtherFee $otherFee
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(OtherFee $otherFee, Request $request)
    {
        $this->validate($request, [
            'shop_id' => 'required|exists:shops,id',
            'pay_at' => 'required|date',
            'money' => 'required|numeric',
            'remark' => 'required|string',
        ], [
            'shop_id.required' => '请选择店铺',
            'pay_at.required' => '支出时间不能为空', 'pay_at.date' => '支出时间格式不对',
            'money.required' => '请输入金额', 'money.numeric' => '金额必须为数字',
            'remark.required' => '备注不能为空'
        ]);

        $otherFee->fill($request->only(['shop_id', 'pay_at', 'money', 'remark']));
        if ($otherFee->save()) {
            return response()->json(['msg' => '修改成功']);
        }
        return response()->json(['error' => '修改失败'], 400);
    }

    /**
     * 删除
     * @param OtherFee $otherFee
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(OtherFee $otherFee)
    {
        if ($otherFee->delete()) {
            return response()->json(['msg' => '删除成功']);
        }
        return response()->json(['error' => '删除失败,请稍后重试']);
    }
}
