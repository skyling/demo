<?php

namespace Demo\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Demo\Http\Controllers\Controller;
use Demo\Model\Import;
use Demo\Util\Helper;

class ImportController extends Controller
{
    public function lists(Request $request)
    {
        $data = Import::query()
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
     * 导入
     * @param Import $import
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Import $import)
    {
        if ($import->delete()) {
            return response()->json(['msg' => '删除成功']);
        }
        return response()->json(['error' => '删除失败,请稍后重试']);
    }
}
