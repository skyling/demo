<?php
/**
 * Created by PhpStorm.
 * User: lifuren
 * Date: 2019/8/3
 * Time: 10:56
 */

namespace Demo\Services;


use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Demo\Exports\DataExport;
use Demo\Imports\LogisticsImport;
use Demo\Imports\OrderImport;
use Demo\Imports\PurchasesImport;

class ImportService
{
    /**
     * 物流单导入
     * @param $filepath
     * @return array
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public static function logisticsImport($filepath)
    {
        $import = new LogisticsImport();
        Excel::import($import, $filepath);
        $data = $import->ret;
        $matchLogistics = MatchService::logisticsMatch(new Request());
        $matchLogistics = count($matchLogistics);
        $export = new DataExport(collect($data));
        $path = '/downloads/物流导入结果_' . date('YmdHis') . '.xlsx';
        Excel::store($export, $path);
        return compact('matchLogistics', 'path');
    }

    /**
     * 采购单导入
     * @param $filepath
     * @return array
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public static function purchaseImport($filepath)
    {
        $import = new PurchasesImport();
        Excel::import($import, $filepath);
        $data = $import->ret;
        $matchGoods = MatchService::goodsMatch(new Request());
        $matchGoods = count($matchGoods);
        $export = new DataExport(collect($data));
        $path = '/downloads/采购单导入结果_' . date('YmdHis') . '.xlsx';
        Excel::store($export, $path);
        return compact('matchGoods', 'path');
    }

    /**
     * 订单导入
     * @param $filepath
     * @return array
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public static function orderImport($filepath)
    {
        $import = new OrderImport();
        Excel::import($import, $filepath);
        $data = $import->ret;
        $matchGoods = MatchService::goodsMatch(new Request());
        $matchGoods = count($matchGoods);
        $matchLogistics = MatchService::logisticsMatch(new Request());
        $matchLogistics = count($matchLogistics);
        $export = new DataExport(collect($data));
        $path = '/downloads/订单导入结果_' . date('YmdHis') . '.xlsx';
        Excel::store($export, $path);
        return compact('path', 'matchGoods', 'matchLogistics');
    }
}