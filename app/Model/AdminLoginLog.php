<?php

namespace Demo\Model;

use Illuminate\Database\Eloquent\Model;

class AdminLoginLog extends Model
{
    protected $fillable = ['admin_id', 'ip'];

    public static function log($uid, $ip)
    {
        return self::create(['admin_id'=>$uid, 'ip'=>ip2long($ip)]);
    }
}
