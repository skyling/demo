<?php

namespace Demo\Exports;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Demo\Model\OrderRefund;

class OrderRefundExport implements FromCollection
{
    private $params;
    private $data = [
        ['ID', '店铺名称', '订单编号', '订单金额', '退款金额', '退款时间', '退款原因', '结算ID', '结算时间'],
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
        OrderRefund::query()
            ->listWhere($request)
            ->chunkById(100, function ($data) {
                foreach ($data as $refund) {
                    $this->data[] = [
                        $refund->id,
                        "\t" . $refund->shop->name,
                        "\t" . $refund->order->order_no,
                        $refund->order->money . '',
                        $refund->money . '',
                        $refund->refund_at,
                        "\t" . $refund->remark,
                        $refund->settlement_id,
                        $refund->settlement_at,
                    ];
                }
            });
        return collect($this->data);
    }
}
