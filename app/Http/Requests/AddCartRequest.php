<?php

namespace App\Http\Requests;

use App\Models\ProductSku;

class AddCartRequest extends Request
{

    public function rules()
    {
        return [
            'sku_id' => [
                'required',
                /**
                 * 闭包校验规则在 Laravel 5.5 开始支持 如果传来的是数组 则会循环校验  参考 OrderRequest
                 * @param 1 字段名称
                 * @param 2 字段的值
                 * @param 3 错误信息
                 */
                function ($attribute, $value, $fail) {
                    if(!$sku = ProductSku::find($value)) {      // 单品是否存在
                        return $fail('该商品不存在');
                    }
                    if(!$sku->product->on_sale) {
                        return $fail('该商品未上架');
                    }
                    if($sku->stock === 0) {
                        return $fail('该商品已售完');
                    }
                    if($this->amount > 0 && $this->amount > $sku->stock) {
                        return $fail('该商品库存不足');
                    }
                }
            ],
            'amount' => 'required|min:1|integer'
        ];
    }

    public function messages()
    {
        return [
            'sku_id.required' => '请选则商品'
        ];
    }

    public function attributes()
    {
        return [
            'amount' => '商品数量'
        ];
    }
}
