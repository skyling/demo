<?php

namespace Demo\Exports;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Demo\Model\Order;

class OrderGoodsSheet implements FromCollection, WithTitle
{
    private $params;
    private $data = [
        ['ID', '店铺名称', '订单编号', '发货时间', '商品编号', '商品SKU', '个数', '商品成本', '是否匹配', '结算ID', '结算时间'],
    ];

    public function __construct($params)
    {
        $this->params = $params;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $request = new Request($this->params);
        Order::query()
            ->listWhere($request)
            ->chunkById(100, function ($data) {
                foreach ($data as $order) {
                    if ($order->goods->count()) {
                        foreach ($order->goods as $goods) {
                            $this->data[] = [
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
                }
            });
        return collect($this->data);
    }

    public function title(): string
    {
        return '订单商品';
    }
}
