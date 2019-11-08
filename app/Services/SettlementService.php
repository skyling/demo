<?php
/**
 * Created by PhpStorm.
 * User: lifuren
 * Date: 2019/8/4
 * Time: 15:39
 */

namespace Demo\Services;


use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Demo\Model\Order;
use Demo\Model\OrderGoods;
use Demo\Model\OrderLogistics;
use Demo\Model\OrderRefund;
use Demo\Model\OtherFee;
use Demo\Model\Settlement;
use Demo\Model\SettlementItem;
use Demo\Util\Helper;

class SettlementService
{
    /**
     * 结算订单项
     * @param $shopId
     * @param $month
     * @param $start
     * @param $end
     * @return array
     */
    public static function items($shopId, $month, $start)
    {
        $end = Carbon::parse($month)->endOfMonth();
        $start = $start ?: Carbon::parse('1970-01-01');
        $mv = Carbon::parse($month)->format('Y年m月:');
        // 订单
        $order = Order::query()
            ->where('shop_id', $shopId)
            ->whereBetween('shipping_at', [$start, $end])
            ->selectRaw('IFNULL(SUM(IF(settlement_id=0, money, 0)), 0) money, 
            count(*) all_count,
            count(IF(settlement_id=0 and money>0, 1, null)) settlement_count,
            count(IF(settlement_id>0, 1, null)) settlemented_count,
            count(IF(money=0, 1, null)) cant_settlement_count')
            ->first();
        $order = $order->toArray();
        $order['remark'] = $mv . '订单金额(已扣手续费)' . round($order['money'], 2);
        // 进货商品
        $purchase = OrderGoods::query()
            ->where('shop_id', $shopId)
            ->whereBetween('shipping_at', [$start, $end])
            ->selectRaw('IFNULL(SUM(IF(settlement_id=0, money, 0)), 0) money, 
            count(*) all_count,
            count(IF(settlement_id=0 and purchases_id>0, 1, null)) settlement_count,
            count(IF(settlement_id>0, 1, null)) settlemented_count,
            count(IF(purchases_id=0, 1, null)) cant_settlement_count')
            ->first();
        $purchase = $purchase->toArray();
        $purchase['remark'] = $mv . '订单商品金额';
        // 物流
        $logistics = OrderLogistics::query()
            ->where('shop_id', $shopId)
            ->whereBetween('shipping_at', [$start, $end])
            ->selectRaw('IFNULL(SUM(IF(settlement_id=0, money, 0)), 0) money, 
            count(*) all_count,
            count(IF(settlement_id=0 and logistics_id>0, 1, null)) settlement_count,
            count(IF(settlement_id>0, 1, null)) settlemented_count,
            count(IF(logistics_id=0, 1, null)) cant_settlement_count')
            ->first();
        $logistics = $logistics->toArray();
        $logistics['remark'] = $mv . '订单物流金额';
        // 退款订单
        $refund = OrderRefund::query()
            ->where('shop_id', $shopId)
            ->whereBetween('refund_at', [$start, $end])
            ->selectRaw('IFNULL(SUM(IF(settlement_id=0, money, 0)), 0) money, 
            count(*) all_count,
            count(IF(settlement_id=0, 1, null)) settlement_count,
            count(IF(settlement_id>0, 1, null)) settlemented_count,
            0 cant_settlement_count')
            ->first();
        $refund = $refund->toArray();
        $refund['remark'] = $mv . '订单退款金额';
        // 其他费用
        $other = OtherFee::query()
            ->where('shop_id', $shopId)
            ->whereBetween('pay_at', [$start, $end])
            ->selectRaw('IFNULL(SUM(IF(settlement_id=0, money, 0)), 0) money, 
            count(*) all_count,
            count(IF(settlement_id=0, 1, null)) settlement_count,
            count(IF(settlement_id>0, 1, null)) settlemented_count,
            0 cant_settlement_count')
            ->first();
        $other = $other->toArray();
        $other['remark'] = $mv . '其他费用金额';
        $data = compact('order', 'purchase', 'logistics', 'refund', 'other');
        $del = [];
        foreach ($data as $key => &$item) {
            if ($key != 'order') {
                $item['money'] = -$item['money'];
            } else {
                $item['money'] = $item['money'] * Helper::getOrderRate();
            }
            $item['money'] = round($item['money'], 2);
            if ($item['money'] == 0 && $item['cant_settlement_count'] == 0) {
                $del[] = $key;
            }
        }
        unset($item);
        foreach ($del as $s) {
            unset($data[$s]);
        }
        return $data;
    }

    /**
     * 结算
     * @param $shopId
     * @param $month
     * @param $start
     * @param string $remark
     * @return bool
     */
    public static function confirm($shopId, $month, $start, $remark = '')
    {
        $adminUserId = Auth::user()->id;
        $end = Carbon::parse($month)->endOfMonth();
        $start = $start ?: Carbon::parse('1970-01-01');
        $now = Carbon::now();
        DB::beginTransaction();
        try {
            $data = self::items($shopId, $month, $start);
            $settlement = new Settlement();
            $settlement->fill([
                'shop_id' => $shopId,
                'money' => 0,
                'remark' => $remark,
                'month' => $month,
                'admin_user_id' => $adminUserId,
            ]);
            $settlement->save();
            foreach ($data as $key => $item) {
                $data = [
                    'settlement_id' => $settlement->id,
                    'shop_id' => $shopId,
                    'type' => 0,
                    'remark' => $item['remark'],
                    'money' => 0,
                ];
                $settlement->money = $settlement->money + $item['money'];
                switch ($key) {
                    case 'order':
                        $data['type'] = SettlementItem::TYPE_ORDER;
                        $data['money'] = $item['money'];
                        Order::query()
                            ->where('shop_id', $shopId)
                            ->whereBetween('shipping_at', [$start, $end])
                            ->where('settlement_id', 0)
                            ->where('money', '>', '0')
                            ->update(['settlement_id' => $settlement->id, 'settlement_at' => $now]);
                        break;
                    case 'purchase':
                        $data['type'] = SettlementItem::TYPE_ORDER_GOODS;
                        $data['money'] = $item['money'];
                        OrderGoods::query()
                            ->where('shop_id', $shopId)
                            ->whereBetween('shipping_at', [$start, $end])
                            ->where('settlement_id', 0)
                            ->where('purchases_id', '>', '0')
                            ->update(['settlement_id' => $settlement->id, 'settlement_at' => $now]);
                        break;
                    case 'logistics':
                        $data['type'] = SettlementItem::TYPE_ORDER_LOGISTICS;
                        $data['money'] = $item['money'];
                        OrderLogistics::query()
                            ->where('shop_id', $shopId)
                            ->whereBetween('shipping_at', [$start, $end])
                            ->where('settlement_id', 0)
                            ->where('logistics_id', '>', '0')
                            ->update(['settlement_id' => $settlement->id, 'settlement_at' => $now]);
                        break;
                    case 'refund':
                        $data['type'] = SettlementItem::TYPE_ORDER_REFUND;
                        $data['money'] = $item['money'];
                        OrderRefund::query()
                            ->where('shop_id', $shopId)
                            ->whereBetween('refund_at', [$start, $end])
                            ->where('settlement_id', 0)
                            ->update(['settlement_id' => $settlement->id, 'settlement_at' => $now]);
                        break;
                    case 'other':
                        $data['type'] = SettlementItem::TYPE_OTHERS;
                        $data['money'] = $item['money'];
                        OtherFee::query()
                            ->where('shop_id', $shopId)
                            ->whereBetween('pay_at', [$start, $end])
                            ->where('settlement_id', 0)
                            ->update(['settlement_id' => $settlement->id, 'settlement_at' => $now]);
                        break;
                }
                if ($data['type'] == 0) {
                    continue;
                }
                $settlementItem = new SettlementItem();
                $settlementItem->fill($data);
                $settlementItem->save();
            }
            $settlement->save();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();
        }
        return false;
    }

    /**
     * 订单利润
     * @param $orderId
     * @return bool
     */
    public static function orderProfile($orderId)
    {
        // 订单本身金额
        // 订单进货成本
        // 订单物流成本
        // 订单退款
        $order = Order::query()->where('id', $orderId)->first();
        if (!$order) {
            return false;
        }
        $profit = $order->money * Helper::getOrderRate();
        if ($order->goods->count() > 0) {
            foreach ($order->goods as $goods) {
                $profit -= $goods->money;
            }
        }
        if ($order->logistics->count() > 0) {
            foreach ($order->logistics as $logistic) {
                $profit -= $logistic->money;
            }
        }
        if ($order->refund->count()) {
            foreach ($order->refund as $refund) {
                $profit -= $refund->money;
            }
        }
        if ($order->profit == $profit) {
            return false;
        }
        $order->profit = $profit;
        $order->profit_at = Carbon::now();
        return $order->save();
    }
}