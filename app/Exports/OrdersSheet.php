<?php

namespace Demo\Exports;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Demo\Model\Order;

class OrdersSheet implements FromCollection, WithTitle
{
    private $params;
    private $data = [
        ['ID', '店铺名称', '订单号', '发货时间', '订单金额(USD)', '订单金额', '下单时间', '付款时间', '国家', '订单利润', '利润计算时间', '结算ID', '结算时间'],
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
                    $this->data[] = [
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
                }
            });
        return collect($this->data);
    }

    public function title(): string
    {
        return '订单';
    }
}
