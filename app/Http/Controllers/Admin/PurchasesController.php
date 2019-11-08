<?php

namespace Demo\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Demo\Http\Controllers\Controller;
use Demo\Model\Export;
use Demo\Model\Import;
use Demo\Model\Purchase;
use Demo\Util\Helper;

class PurchasesController extends Controller
{
    /**
     * 采购列表
     * @param Request $request
     * @return array
     */
    public function lists(Request $request)
    {
        $data = Purchase::query()
            ->with(['orderGoods', 'orderGoods.order', 'orderGoods.shop'])
            ->listWhere($request)
            ->paginate($request->input('limit', 20));
        return Helper::apiPaginate($data);
    }

    /**
     * 创建采购单
     * @param Request $request
     */
    public function create(Request $request)
    {

    }

    /**
     * 更新采购单
     * @param Purchase $purchase
     * @param Request $request
     */
    public function update(Purchase $purchase, Request $request)
    {

    }

    /**
     * 删除采购单
     * @param Purchase $purchase
     */
    public function destroy(Purchase $purchase)
    {
        if ($purchase->delete()) {
            return response()->json(['msg' => '删除成功']);
        }
        return response()->json(['error' => '删除失败']);
    }

    /**
     * 导入采购单
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
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
            'type' => Import::TYPE_PURCHASE
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
        $export = Export::saveExport(Export::TYPE_PURCHASE, $request);
        return response()->json(['msg' => '导出任务新建成功,任务ID为:' . $export->id]);
    }
}
