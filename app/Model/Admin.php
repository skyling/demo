<?php

namespace Demo\Model;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject as AuthenticatableUserContract;

class Admin extends Model implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract,
    AuthenticatableUserContract
{
    use Notifiable, Authenticatable, Authorizable, CanResetPassword, HasRoles;

    const STATUS_ON = 1; // 启用
    const STATUS_OFF = 0; // 禁用
    const GUARD_NAME = 'admin';
    public $guard_name = self::GUARD_NAME;
    protected $appends = ['role', 'shops'];

    protected $fillable = [
        'username', 'password', 'status', 'email', 'shop_id'
    ];

    protected $hidden = [
        'password'
    ];

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * 角色
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'admin_has_roles', 'admin_id', 'role_id');
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    public function getRoleAttribute()
    {
        return $this->roles()->pluck('name')->first();
    }

    public function getShopIdAttribute()
    {
        if (!isset($this->attributes['shop_id'])) {
            return;
        }
        return $this->attributes['shop_id'] ? explode(',', $this->attributes['shop_id']) : [];
    }

    public function setShopIdAttribute($value)
    {
        $v = $value ? implode(',', $value) : '';
        $this->attributes['shop_id'] = $v;
    }

    public function getShopsAttribute()
    {
        $shopIds = $this->shop_id;
        if (empty($shopIds)) {
            return [];
        }
        return Shop::query()->select('id', 'name')->where(function ($query) use ($shopIds) {
            if (!empty($shopIds)) {
                $query->whereIn('id', $shopIds);
            }
        })->get();
    }
}
