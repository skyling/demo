<?php

namespace Demo\Exports;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Demo\Model\Logistics;
use Demo\Model\Purchase;

class PurchasesExport implements FromCollection
{
    private $params;
    private $data = [
        ['ID', '采购单号', '商品SKU', '商品编号', '购买时间', '到货时间', '商品单价', '商品个数', '进货总价', '运费', '单个商品均价', '采购员', '匹配次数', '匹配时间']
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
        Purchase::query()
            ->listWhere($request)
            ->chunkById(100, function ($data) {
                foreach ($data as $purchase)
                    $this->data[] = [
                        $purchase->id,
                        "\t" . $purchase->purchases_no,
                        "\t" . $purchase->sku,
                        $purchase->buy_at,
                        $purchase->received_at,
                        $purchase->per_money,
                        $purchase->count,
                        $purchase->money,
                        $purchase->shipping_money,
                        $purchase->avg_money,
                        "\t" . $purchase->operator,
                        $purchase->match_count,
                        $purchase->match_last_at,
                    ];
            });
        return collect($this->data);
    }
}
