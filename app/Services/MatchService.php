<?php
/**
 * Created by PhpStorm.
 * User: lifuren
 * Date: 2019/8/2
 * Time: 22:38
 */

namespace Demo\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Demo\Model\OrderGoods;
use Demo\Model\OrderLogistics;

class MatchService
{
    /**
     * 订单物流匹配
     * @param null $orderLogisticsId
     */
    public static function logisticsMatch($request)
    {
        $ret = [];
        // 匹配物流
        OrderLogistics::query()
            ->listWhere($request)
            ->where('logistics_id', 0)
            ->where('settlement_id', 0)
            ->chunkById(100, function ($data) use (&$ret) {
                foreach ($data as $orderLogistics) {
                    if (!$orderLogistics->logistics || !$orderLogistics->logistics->count()) {
                        continue;
                    }
                    $count = $orderLogistics->logistics->order_count;
                    $orderLogistics->money = $orderLogistics->logistics->money / $count;
                    $orderLogistics->weight = $orderLogistics->logistics->weight / $count;
                    $orderLogistics->logistics_id = $orderLogistics->logistics->id;
                    $orderLogistics->save();
                    $orderLogistics->logistics->match_count++;
                    $orderLogistics->logistics->match_last_at = Carbon::now();
                    $orderLogistics->logistics->save();
                    $ret[] = $orderLogistics->id;
                    SettlementService::orderProfile($orderLogistics->order_id);
                    Log::info('order logistics:' . json_encode($orderLogistics));
                }
            });
        return $ret;
    }

    /**
     * 订单商品匹配
     * @param $orderGoodsId
     * @return array
     */
    public static function goodsMatch($request)
    {
        $ret = [];
        // 匹配商品
        OrderGoods::query()
            ->where('purchases_id', 0)
            ->where('settlement_id', 0)
            ->listWhere($request)
            ->chunkById(100, function ($data) use (&$ret) {
                foreach ($data as $orderGoods) {
                    // 没有匹配的物流单号
                    if (!$orderGoods->purchases || !$orderGoods->purchases->count()) {
                        continue;
                    }
                    $matchPurchase = null;
                    foreach ($orderGoods->purchases as $purchase) {
                        $matchPurchase = $matchPurchase ?: $purchase;
                        if ($purchase->match_count < $purchase->count) {
                            $matchPurchase = $purchase;
                        }
                    }
                    $orderGoods->purchases_id = $matchPurchase->id;
                    $orderGoods->money = $matchPurchase->avg_money * $orderGoods->count;
                    $orderGoods->save();
                    $matchPurchase->match_count++;
                    $matchPurchase->match_last_at = Carbon::now();
                    $matchPurchase->save();
                    $ret[] = $orderGoods->id;
                    SettlementService::orderProfile($orderGoods->order_id);
                    Log::info('order goods:' . json_encode($orderGoods));
                }
            });
        return $ret;
    }
}