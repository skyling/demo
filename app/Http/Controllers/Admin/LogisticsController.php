<?php

namespace Demo\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Demo\Http\Controllers\Controller;
use Demo\Model\Export;
use Demo\Model\Import;
use Demo\Model\Logistics;
use Demo\Util\Helper;

class LogisticsController extends Controller
{
    /**
     * 订单列表
     * @param Request $request
     * @return array
     */
    public function lists(Request $request)
    {
        $data = Logistics::query()
            ->with(['orderLogistics', 'orderLogistics.order', 'orderLogistics.shop'])
            ->listWhere($request)
            ->paginate($request->input('limit', 20));
        return Helper::apiPaginate($data);
    }

    /**
     * 创建订单
     * @param Request $request
     */
    public function create(Request $request)
    {

    }

    /**
     * 更新订单
     * @param Logistics $order
     * @param Request $request
     */
    public function update(Logistics $order, Request $request)
    {

    }

    /**
     * 删除订单
     * @param Logistics $logistics
     */
    public function destroy(Logistics $logistics)
    {
        if ($logistics->delete()) {
            return response()->json(['msg' => '删除成功']);
        }
        return response()->json(['error' => '删除失败']);
    }

    /**
     * 导入订单
     * @param Request $request
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
            'type' => Import::TYPE_LOGISTICS
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
        $export = Export::saveExport(Export::TYPE_LOGISTICS, $request);
        return response()->json(['msg' => '导出任务新建成功,任务ID为:' . $export->id]);
    }
}
