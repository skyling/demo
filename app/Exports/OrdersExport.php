<?php

namespace Demo\Exports;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Demo\Model\Order;

class OrdersExport implements WithMultipleSheets
{
    private $params;

    public function __construct($params)
    {
        $this->params = $params;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collectionbak()
    {
        $request = new Request($this->params);
        Order::query()
            ->listWhere($request)
            ->chunkById(100, function ($data) {
                foreach ($data as $order) {
                    $this->data[] = [
                        '订单',
                        $order->id,
                        "\t" . $order->shop->name,
                        "\t" . $order->order_no,
                        $order->shipping_at,
                        $order->origin_money . '',
                        $order->money . '',
                        $order->order_at,
                        $order->pay_at,
                        "\t" . $order->country_name,
                        $order->profit . '',
                        $order->profit_at,
                        $order->settlement_id,
                        $order->settlement_at,
                    ];
//                    ['订单商品', 'ID', '店铺名称', '订单编号', '商品编号', '发货时间', '商品SKU', '个数', '商品成本', '是否匹配', '结算ID', '结算时间'],
                    if ($order->goods->count()) {
                        foreach ($order->goods as $goods) {
                            $this->data[] = [
                                '订单商品',
                                $goods->id,
                                "\t" . $order->shop->name,
                                "\t" . $order->order_no,
                                "\t" . $goods->goods_no,
                                $goods->shipping_at,
                                "\t" . $goods->sku,
                                $goods->count . '',
                                $goods->money . '',
                                $goods->purchases_id ? '是' : '否',
                                $goods->settlement_id,
                                $goods->settlement_at,
                            ];
                        }
                    }
//                    ['订单物流', 'ID', '店铺名称', '订单编号', '包裹号', '物流编号', '发货时间', '物流渠道', '物流成本', '重量', '是否匹配',  '结算ID', '结算时间'],
                    if ($order->logistics->count()) {
                        foreach ($order->logistics as $logistic) {
                            $this->data[] = [
                                '订单物流',
                                $logistic->id,
                                "\t" . $order->shop->name,
                                "\t" . $order->order_no,
                                $logistic->shipping_at,
                                "\t" . $logistic->package_no,
                                "\t" . $logistic->shipping_no,
                                "\t" . $logistic->name,
                                $logistic->money . '',
                                $logistic->weight . '',
                                $logistic->logistics_id ? '是' : '否',
                                $logistic->settlement_id,
                                $logistic->settlement_at,
                            ];
                        }
                    }
//                    ['订单退款', 'ID', '店铺名称', '订单编号', '退款金额', '退款时间', '退款原因', '结算ID', '结算时间'],
                    if ($order->refund->count()) {
                        foreach ($order->refund as $refund) {
                            $this->data[] = [
                                '订单退款',
                                $refund->id,
                                "\t" . $order->shop->name,
                                "\t" . $order->order_no,
                                $refund->refund_at,
                                $refund->money . '',
                                "\t" . $refund->remark,
                                $refund->settlement_id,
                                $refund->settlement_at,
                            ];
                        }
                    }
                }
            });
        return collect($this->data);
    }

    public function sheets(): array
    {
        $sheets = [];
        $sheets[] = new OrdersSheet($this->params);
        $sheets[] = new OrderGoodsSheet($this->params);
        $sheets[] = new OrderLogisticsSheet($this->params);
        $sheets[] = new OrderRefundSheet($this->params);
        return $sheets;
    }
}
