<?php

namespace Demo\Exports;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Demo\Model\Order;

class OrderRefundSheet implements FromCollection, WithTitle
{
    private $params;
    private $data = [
        ['ID', '店铺名称', '订单编号', '退款时间', '退款金额', '退款原因', '结算ID', '结算时间'],
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
                    if ($order->refund->count()) {
                        foreach ($order->refund as $refund) {
                            $this->data[] = [
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

    public function title(): string
    {
        return '订单退款';
    }
}
