<?php

namespace Demo\Exports;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Demo\Model\Order;

class OrderLogisticsSheet implements FromCollection, WithTitle
{
    private $params;
    private $data = [
        ['ID', '店铺名称', '订单编号', '发货时间', '包裹号', '物流编号', '物流渠道', '物流成本', '重量', '是否匹配', '结算ID', '结算时间'],
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
                    if ($order->logistics->count()) {
                        foreach ($order->logistics as $logistic) {
                            $this->data[] = [
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
                }
            });
        return collect($this->data);
    }

    public function title(): string
    {
        return '订单物流';
    }
}
