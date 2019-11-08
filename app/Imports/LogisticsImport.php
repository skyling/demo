<?php

namespace Demo\Imports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithLimit;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Demo\Model\Logistics;
use Demo\Util\Helper;

class LogisticsImport implements ToCollection, WithStartRow, WithHeadingRow, WithChunkReading
{
    use Importable, ExportTrait;
    public $ret;
    static $columns = [
        '0' => '国际物流单号',
        '1' => '创建时间',
        '2' => '物流服务名称',
        '3' => '订单重量',
        '4' => '金额',
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
            $shippingNo = trim($item['国际物流单号']);
            if (!$shippingNo) {
                $this->ret[] = $item + ['处理失败:物流单号不能为空'];
                continue;
            }
            $attributes = [
                'shipping_no' => $shippingNo,
            ];
            $money = floatval($item['金额']);
            if (empty($money)) {
                $this->ret[] = $item + ['处理失败:金额必须大于0'];
                continue;
            }
            $weight = floatval($item['订单重量']);
            $weight = $weight > 10 ? $weight / 1000 : $weight;
            $values = [
                'money' => floatval($money),
                'name' => $item['物流服务名称'],
                'weight' => $weight,
                'shipping_at' => Helper::parseDate($item['创建时间']),
            ];
            $ret = Logistics::query()->updateOrCreate($attributes, $values);
            $this->ret[] = $item + ['处理成功,ID:' . $ret->id];
        }
    }
}
