<?php

namespace Demo\Imports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Demo\Model\Shop;
use Demo\Model\Order;
use Demo\Model\OrderGoods;
use Demo\Model\OrderLogistics;
use Demo\Model\OrderRefund;
use Demo\Util\Helper;

class OrderImport implements ToCollection, WithStartRow, WithChunkReading, WithHeadingRow
{
    use Importable, ExportTrait;
    public $ret;
    static $columns = [
        '0' => '包裹号',
        '1' => '订单号',
        '2' => '店铺账号',
        '3' => '退款金额',
        '4' => '退款理由',
        '5' => '退款时间',
        '6' => '下单时间',
        '7' => '付款时间',
        '8' => '发货时间',
        '9' => '订单金额',
        '10' => '产品ID',
        '11' => 'SKU',
        '12' => '中文国家名',
        '13' => '国家二字码',
        '14' => '物流方式',
        '15' => '运单号',
        '16' => '产品数量',
        '17' => '汇率',
    ];

    /**
     * @param Collection $collection
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function collection(Collection $collection)
    {
        if ($this->validateHeading($collection[0])) {
            unset($collection[0]);
        }
        $shops = Shop::query()->pluck('id', 'name')->toArray();
        foreach ($collection as $item) {
            $item = $item->toArray();
            $shopName = $item['店铺账号'];
            $shopId = $shops[$shopName] ?? 0;
            if (!$shopId) {
                // 店铺不存在
                $this->ret[] = $item + ['店铺不存在'];
                continue;
            }
            $orderNo = $item['订单号'];
            if (!$orderNo) {
                $this->ret[] = $item + ['订单号不能为空'];
                continue;
            }
            $exchangeRate = floatval($item['汇率']) ?: Order::EXCHANGE_RATE;
            $originMoney = floatval($item['订单金额']);
            // 订单信息
            $attributes = [
                'order_no' => $orderNo,
                'shop_id' => $shopId
            ];
            $values = [
                'shipping_at' => Helper::parseDate($item['发货时间']),
                'pay_at' => Helper::parseDate($item['付款时间']),
                'order_at' => Helper::parseDate($item['下单时间']),
                'country_name' => $item['中文国家名'],
                'country_code' => $item['国家二字码'],
                'exchange_rate' => $exchangeRate,
            ];
            if ($originMoney) {
                $values += [
                    'origin_money' => $originMoney,
                    'money' => floatval($originMoney * $exchangeRate)
                ];
            }
            $order = Order::query()->where($attributes)->first();
            if ($order) {
                $order->settlement_id == 0 && $order->update($values);
            } else {
                $order = Order::query()->create($attributes + $values);
            }
//            $order = Order::query()
//                ->where('order_no', $orderNo)
//                ->where('shop_id', $shopId)->updateOrCreate($attributes, $values);
            $refundMoney = floatval($item['退款金额']);

            // 退款信息
            if ($refundMoney > 0) {
                $attributes = [
                    'order_id' => $order->id,
                    'shop_id' => $shopId,
                ];
                $values = [
                    'money' => $refundMoney * $exchangeRate,
                    'origin_money' => $refundMoney,
                    'exchange_rate' => $exchangeRate,
                    'refund_at' => Helper::parseDate($item['退款时间']),
                    'remark' => $item['退款理由'],
                ];
                $orderRefund = OrderRefund::query()->where($attributes)->first();
                if ($orderRefund) {
                    $orderRefund->settlement_id == 0 && $orderRefund->update($values);
                } else {
                    OrderRefund::query()->create($attributes + $values);
                }
                // OrderRefund::query()->updateOrCreate($attributes, $values);
            }

            // 物流信息
            $attributes = [
                'shop_id' => $shopId,
                'order_id' => $order->id,
                'shipping_no' => $item['运单号'],
                'package_no' => $item['包裹号'],
            ];
            $values = [
                'name' => $item['物流方式'],
                'shipping_at' => Helper::parseDate($item['发货时间']),
            ];
            $logistics = OrderLogistics::query()->firstOrCreate($attributes, $values);

            // 商品信息
            $attributes = [
                'shop_id' => $shopId,
                'order_id' => $order->id,
                'order_logistic_id' => $logistics->id,
                'goods_no' => $item['产品ID'],
                'sku' => $item['SKU'],
            ];
            $values = [
                'shipping_at' => Helper::parseDate($item['发货时间']),
                'count' => intval($item['产品数量']),
            ];
            OrderGoods::query()->firstOrCreate($attributes, $values);
            $this->ret[] = $item + ['导入成功,订单ID:' . $order->id];
        }
    }

}
