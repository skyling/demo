<?php

namespace Demo\Model;

use Demo\Model\UserAddress;
use Demo\Model\UserLoginLog;
use Demo\Util\Helper;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Hashing\BcryptHasher;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use phpseclib\Crypt\Hash;

class User extends Authenticatable
{
    use Notifiable,HasApiTokens;
    const EMAIL_VALIDATED = 1;
    const STATUS_ON = 1;
    const STATUS_OFF = 0;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'remember_token', 'email_validated', 'gender', 'register_at', 'register_ip', 'avatar', 'status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token','avatar_url',
    ];

    protected $appends = ['avatar_url'];

    /**
     * 列表查询条件
     * @param $query
     * @param Request $request
     * @return mixed
     */
    public function scopeListWhere($query, Request $request)
    {
        $where = Helper::arrayFilter($request->only(['status', 'email_validated', 'email', 'gender']));
        if ($where) {
            foreach ($where as $key=>$value) {
                $query->where($key, $value);
            }
        }

        $where = Helper::arrayFilter($request->only(['name']));
        if ($where) {
            foreach ($where as $key=>$value) {
                $query->where($key, 'like', '%'.$value.'%');
            }
        }

        $where = Helper::arrayFilter($request->only(['register_at']));
        if ($where) {
            foreach ($where as $key=>$value) {
                $query->whereBetween($key, $value);
            }
        }
        return $query;
    }

    public function getRegisterIpAttribute()
    {
        return long2ip($this->attributes['register_ip']);
    }

    public function setRegisterIpAttribute($value)
    {
        $this->attributes['register_ip'] = ip2long($value);
    }

    /**
     * 查找用户
     * @param $username
     * @return \Illuminate\Database\Eloquent\Model|null|static
     */
    public function findForPassport($username)
    {
        return self::query()->where('email', $username)->where('status', self::STATUS_ON)->first();
    }

    /**
     * 匹配密码
     * @param $password
     * @return bool
     */
    public function validateForPassportPasswordGrant($password)
    {
        return password_verify($password, $this->getAuthPassword()) || ($password == $this->getAuthPassword());
    }

    /**
     * 头像URL
     * @return string
     */
    public function getAvatarUrlAttribute()
    {
        return $this->avatar ? Helper::uploadsUrl($this->avatar) : '';
    }

    public function addresses()
    {
        return $this->hasMany(UserAddress::class, 'user_id', 'id');
    }

    public function loginLogs()
    {
        return $this->hasMany(UserLoginLog::class, 'user_id', 'id');
    }

}
