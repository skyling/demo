<?php

namespace Demo\Exports;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Demo\Model\Logistics;

class LogisticsExport implements FromCollection
{
    private $params;
    private $data = [
        ['ID', '物流单号', '物流金额', '物流名称', '包裹重量', '发货时间', '匹配次数', '最后匹配时间']
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
        Logistics::query()
            ->listWhere($request)
            ->chunkById(100, function ($data) {
                foreach ($data as $logistic) {
                    $this->data[] = [
                        $logistic->id,
                        "\t" . $logistic->shipping_no,
                        $logistic->money . '',
                        "\t" . $logistic->name,
                        $logistic->weight . '',
                        $logistic->shipping_at,
                        $logistic->match_count . '',
                        $logistic->match_last_at
                    ];
                }
            });
        return collect($this->data);
    }
}
