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
use Demo\Exports\LogisticsExport;
use Demo\Exports\OrderGoodsExport;
use Demo\Exports\OrderLogisticsExport;
use Demo\Exports\OrderRefundExport;
use Demo\Exports\OrdersExport;
use Demo\Exports\PurchasesExport;
use Demo\Imports\LogisticsImport;
use Demo\Imports\OrderImport;
use Demo\Imports\PurchasesImport;
use Demo\Model\OrderLogistics;

class ExportService
{
    public static function logisticsExport($params)
    {
        $path = '/downloads/物流导出_' . date('YmdHis') . mt_rand(1000, 9999) . '.xlsx';
        $export = new LogisticsExport($params);
        Excel::store($export, $path);
        return $path;
    }

    public static function purchaseExport($params)
    {
        $path = '/downloads/采购单导出_' . date('YmdHis') . mt_rand(1000, 9999) . '.xlsx';
        $export = new PurchasesExport($params);
        Excel::store($export, $path);
        return $path;
    }

    public static function orderExport($params)
    {
        $path = '/downloads/订单导出_' . date('YmdHis') . mt_rand(1000, 9999) . '.xlsx';
        $export = new OrdersExport($params);
        Excel::store($export, $path);
        return $path;
    }

    public static function orderGoodsExport($params)
    {
        $path = '/downloads/订单商品导出_' . date('YmdHis') . mt_rand(1000, 9999) . '.xlsx';
        $export = new OrderGoodsExport($params);
        Excel::store($export, $path);
        return $path;
    }

    public static function orderLogisticsExport($params)
    {
        $path = '/downloads/订单物流导出_' . date('YmdHis') . mt_rand(1000, 9999) . '.xlsx';
        $export = new OrderLogisticsExport($params);
        Excel::store($export, $path);
        return $path;
    }

    public static function orderRefundExport($params)
    {
        $path = '/downloads/订单退款导出_' . date('YmdHis') . mt_rand(1000, 9999) . '.xlsx';
        $export = new OrderRefundExport($params);
        Excel::store($export, $path);
        return $path;
    }
}