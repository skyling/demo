<?php

namespace Demo\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Demo\Http\Controllers\Controller;
use Demo\Model\Export;
use Demo\Util\Helper;

class ExportController extends Controller
{
    public function lists(Request $request)
    {
        $data = Export::query()
            ->with([
                'admin' => function ($query) {
                    $query->select('id', 'username');
                }
            ])
            ->listWhere($request)
            ->orderBy('id', 'desc')
            ->paginate($request->input('limit', 20));
        $data = Helper::apiPaginate($data);
        return $data;
    }

    /**
     * 删除
     * @param Export $export
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Export $export)
    {
        if ($export->delete()) {
            return response()->json(['msg' => '删除成功']);
        }
        return response()->json(['error' => '删除失败,请稍后重试']);
    }
}
