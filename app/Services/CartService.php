<?php

namespace App\Services;

use Auth;
use App\Models\Cart;

class CartService
{
    // 获取购物车所有列表数据
    public function getCarts()
    {
        return Auth::user()->carts()->with('productSku.product')->get();
    }
    
    /**
     * 添加购物车
     * 
     * @param String amount 
     * @param String skuId
     * @return cartObject
     */
    public function add($amount, $skuId)
    {
        $user = Auth::user();
        // 查询该商品是否已经存在 购物车 数据表中
        if($cart = $user->carts()->where('product_sku_id', $skuId)->first()) {
            // 如果存在 则只数量叠加
            $cart->update([
                'amount' => $amount + $cart->amount
            ]);
        }else{
            // 否则就创建一个新的购物车记录 
            $cart = new Cart(['amount' => $amount]);
            // associate() 参数可以是 id 也可以是 对象
            $cart->user()->associate($user);
            $cart->productSku()->associate($skuId);
            $cart->save();
        }
        return $cart;
    }

    /**
     * 删除购物车
     * 
     * @param Array skuIds
     * @return nothing
     */
    public function remove($skuIds)
    {
        if(!is_array($skuIds)){
            $skuIds = [$skuIds];
        }
        Auth::user()->carts()->whereIn('product_sku_id', $skuIds)->delete();
    }
    
}