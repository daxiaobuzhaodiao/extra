<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use App\Models\ProductSku;

class OrderRequest extends Request
{
   
    public function rules()
    {
        return [
            // 判断用户提交的订单 id 是否存在于数据库并且属于用户自己
            'address_id' => [
                'required',
                Rule::exists('user_addresses', 'id')->where('user_id', $this->user()->id)
            ],
            'items' => ['required', 'array'],
            'items.*.sku_id' => [
                'required',
                // 闭包循环 校验 因为用户在把商品加入购物车，再到下单时商品的各个状态都可能发生变化。
                function($attribute, $value, $fail) {
                    // dd($this->items);  // laravel 自动将他转换成了二维数组
                    // $attribute = "items.0.sku_id" 
                    // $value = sku_id 的值
                    if(!$productSku = ProductSku::find($value)) {
                        return $fail('该商品不存在');
                    }
                    if(!$productSku->product->on_sale) {
                        return $fail('该商品未上架');
                    }
                    if($productSku->stock === 0) {
                        return $fail('该商品已售完');
                    }
                    // 通过索引找到用户提交的该商品的数量
                    preg_match('/items\.(\d+)\.sku_id/', $attribute, $m);
                    // dd($m);  // [0 => "items.0.sku_id", 1 => "0"]
                    // 根据索引找到购买的数量
                    $index = $m[1];
                    $amount = $this->items[$index]['amount'];
                    if($amount >0 && $amount > $productSku->stock){
                        return $fail('商品库存不足');
                    }
                }
            ],
            'items.*.amount' => ['required', 'min:1', 'integer']
        ];
    }
}
