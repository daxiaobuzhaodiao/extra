<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Exceptions\InternalException;

class ProductSku extends Model
{
    protected $fillable = [
    	'title',
    	'description',
    	'stock',
    	'price'
    ];

    public function product()
    {
    	return $this->belongsTo('App\Models\Product');
    }

    // 减库存
    public function decreaseStock($amount)
    {
        if($amount < 0) {
            throw new InternalException('减库存不得小于0');
        }
        // update product_skus set stock = stock - $amount where id = $id and stock >= $amount
        // 两个 where 是并列关系
        return $this->newQuery()->where('id', $this->id)->where('stock', '>=', $amount)->decrement('stock', $amount);
    } 
    // 加库存
    public function addStock($amount)
    {
        if($amount < 0) {
            throw new InternalException('加库存不得小于0');
        }
        return $this->increment('stock', $amount);
    }
}
