<?php

namespace Demo\Exports;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Demo\Model\OrderGoods;

class OrderGoodsExport implements FromCollection
{
    private $params;
    private $data = [
        ['ID', '店铺名称', '订单编号', '订单金额', '商品编号', '发货时间', '商品SKU', '个数', '商品成本', '是否匹配', '结算ID', '结算时间'],
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
        OrderGoods::query()
            ->listWhere($request)
            ->chunkById(100, function ($data) {
                foreach ($data as $goods) {
                    $this->data[] = [
                        $goods->id,
                        "\t" . $goods->shop->name,
                        "\t" . $goods->order->order_no,
                        $goods->order->money . '',
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
            });
        return collect($this->data);
    }
}
