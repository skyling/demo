<?php

namespace Demo\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Demo\Model\Purchase;
use Demo\Util\Helper;

class PurchasesImport implements ToCollection, WithStartRow, WithChunkReading, WithHeadingRow
{
    use Importable, ExportTrait;
    public $ret;
    static $columns = [
        '0' => '采购单号',
        '1' => '采购员',
        '2' => '商品编码',
        '3' => '商品SKU',
        '4' => '单价（CNY）',
        '5' => '采购数量',
        '6' => '到货数量',
        '7' => '商品总金额',
        '8' => '运费',
        '9' => '创建时间',
        '10' => '到货时间',
        '11' => '到货状态',
    ];

    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        if ($this->validateHeading($collection[0])) {
            unset($collection[0]);
        }
        foreach ($collection as $item) {
            $item = $item->toArray();
            if (empty($item['采购单号'])) {
                continue;
            }
            $purchaseNo = trim($item['采购单号']);
            $sku = trim($item['商品SKU']);
            $goodsId = trim($item['商品编码']);
            if (!$purchaseNo || !$sku || !$goodsId) {
                $this->ret[] = $item + ['处理失败:采购单号/商品SKU/商品编码 不能为空'];
                continue;
            }
            $attributes = [
                'purchases_no' => $purchaseNo,
                'goods_id' => $goodsId,
                'sku' => $sku,
            ];
            $money = floatval($item['商品总金额']);
            if (empty($money)) {
                $this->ret[] = $item + ['处理失败:商品总金额必须大于0'];
                continue;
            }
            $count = intval($item['到货数量']);
            if (!$count) {
                $this->ret[] = $item + ['处理失败:到货数量为0'];
                continue;
            }
            $shippingMoney = floatval($item['运费']);
            $values = [
                'buy_at' => Helper::parseDate($item['创建时间']),
                'received_at' => Helper::parseDate($item['到货时间']),
                'shipping_money' => $shippingMoney,
                'per_money' => floatval($item['单价（CNY）']),
                'count' => intval($item['到货数量']),
                'money' => floatval($item['商品总金额']),
                'avg_money' => 0,
                'operator' => $item['采购员'],
            ];
            $ret = Purchase::query()->updateOrCreate($attributes, $values);
            $count = Purchase::query()->where('purchases_no', $purchaseNo)->sum('count');
            $shippingMoney = $shippingMoney / $count;
            Purchase::query()->where('purchases_no', $purchaseNo)->update(['avg_money' => DB::raw('money/count+' . $shippingMoney)]);
            $this->ret[] = $item + ['处理成功,ID:' . $ret->id];
        }
    }
}
