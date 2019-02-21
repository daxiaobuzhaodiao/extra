<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AddCartRequest;
use App\Models\CartItem;
use App\Models\ProductSku;

class CartItemsController extends Controller
{
    // 增
    public function add(AddCartRequest $request)
    {
        $user = $request->user();
        $skuId = $request->input('sku_id');
        $amount = $request->input('amount');
        // 查询该商品是否已经存在 购物车 数据表中
        if($cart = $user->cartItems()->where('product_sku_id', $skuId)->first()) {
            // 如果存在 则只数量叠加
            $cart->update([
                'amount' => $amount + $cart->amount
            ]);
        }else{
            // 否则就创建一个新的购物车记录 
            $cart = new CartItem(['amount' => $amount]);
            // associate() 参数可以是 id 也可以是 对象
            $cart->user()->associate($user);
            $cart->productSku()->associate($skuId);
            $cart->save();
        }
        return [];
    }

    // 查
    public function index()
    {
        // with(['productSku.product']) 方法用来预加载购物车里的商品和 SKU 信息。通过 . 提前加载了与商品 SKU 关联的商品
        $cartItems = auth()->user()->cartItems()->with(['productSku.product'])->get(); 
        $addresses = auth()->user()->addresses()->orderBy('last_used_at', 'desc')->get();
        
        return view('cartItems.index', ['cartItems' => $cartItems, 'addresses' => $addresses]);
    }

    // 删
    public function remove(ProductSku $productSku)
    {
        auth()->user()->cartItems()->where('product_sku_id', $productSku->id)->delete();
        return [];
    }
}
