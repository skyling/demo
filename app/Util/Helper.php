<?php

namespace Demo\Util;

use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

/**
 *
 * Author: lifuren <frenlee@163.com>
 * Since: 2018/1/13 17:21
 */
class Helper
{
    const X_API_UUID = 'x-api-uuid';

    /**
     * 获取头部UUID
     *
     * @return uuid
     */
    public static function UUID()
    {
        return md5(request()->header(self::X_API_UUID, ''));
    }

    /**
     * 数组过滤,0不过滤
     * @param $data
     * @return array
     */
    public static function arrayFilter($data)
    {
        $data = array_filter($data, function ($value) {
            return $value === '0' || $value === 0 || !empty($value) || $value === '';
        });
        return $data;
    }

    /**
     * 资源地址拼接为qiniu地址
     * @param $url
     * @return string
     */
    public static function uploadsUrl($url)
    {
        if ($url) {
            $url = (strpos($url, '://') === false) ? (env('APP_URL', '') . $url) : $url;
        }
        return $url;
    }

    public static function formatMoney($number)
    {
        return '$' . number_format($number, 2);
    }

    /**
     * 分页处理
     * @param $data
     * @return array
     */
    public static function apiPaginate(LengthAwarePaginator $data)
    {
        return [
            'data' => $data->items(),
            'total' => $data->total(),
            'per_page' => $data->perPage(),
            'last_page' => $data->lastPage(),
            'current_page' => $data->currentPage(),
        ];
    }

    /**
     * 回复处理
     * @param $code
     * @param $message
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public static function response($code, $message, $data)
    {
        return response()->json(compact('code', 'message', 'data'));
    }

    public static function demoKey($key = null)
    {
        $str = 'yayang_keys';
        if (!$key) {
            return Hash::make('yayang_keys');
        }
        return Hash::check($str, $key);
    }

    public static function parseDate($date)
    {
        preg_match_all("/[\d\:\\/\s\-\_\.]/", $date, $matches);
        $date = join('', $matches[0] ?? []);
        $date = str_replace(['.', '/'], '-', $date);
        return ($date && @strtotime($date) > 0) ? Carbon::parse($date) : null;
    }

    /**
     * 订单比例
     * @return mixed
     */
    public static function getOrderRate()
    {
        return env('ORDER_RATE', 0.92);
    }
}
