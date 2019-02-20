<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = ['amount'];
    
    public $timestamps = false;  // 不维护日期

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
    
    public function productSku()
    {
        return $this->belongsTo('App\Models\ProductSku');
    }
}
