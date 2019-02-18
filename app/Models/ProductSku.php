<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
