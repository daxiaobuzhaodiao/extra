<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function addresses()
    {
        return $this->hasMany('App\Models\UserAddress');
    }

    public function cartItems()
    {
        return $this->hasMany('App\Models\CartItem');
    }
    public function orders()
    {
        return $this->hasMany('App\Models\Order');
    }
    // 通过 中间表 实现 用户与商品之间的收藏关系
    public function favoriteProducts()
    {
        // belongsToMany() 方法用于定义一个多对多的关联，第一个参数是关联的模型类名，第二个参数是中间表的表名。
        return $this->belongsToMany('App\Models\Product', 'user_favorite_products')
            ->withTimestamps()
            ->orderBy('user_favorite_products.created_at', 'desc');// 默认根据中间表的创建时间倒序排序。
    }
}
