<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = ['amount', 'price', 'rating', 'review', 'reviewed_at'];

    protected $dates = ['reviewed_at'];

    public $timestamps = false; // 不维护时间

    // 关联到 Product 模型
    public function product()
    {
        return $this->belongsTo('App\Models\Product');
    }
    // 关联到 ProductSku 模型
    public function productSku()
    {
        return $this->belongsTo('App\Models\ProductSku');
    }
    // 关联到 Order 模型
    public function order()
    {
        return $this->belongsTo('App\Models\Order');
    }
}
