<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AddCartRequest;
use App\Models\ProductSku;
use App\Services\CartService;

class CartsController extends Controller
{
    protected $cartService;
    function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * 增
     * 
     * @param sku_id
     * @param amount
     */
    public function add(AddCartRequest $request)
    {
        $this->cartService->add($request->amount, $request->sku_id);
        return [];
    }

    // 查
    public function index()
    {
        $carts = auth()->user()->carts()->with(['productSku.product'])->get(); 
        $addresses = auth()->user()->addresses()->orderBy('last_used_at', 'desc')->get();
        return view('carts.index', ['carts' => $carts, 'addresses' => $addresses]);
    }

    // 删
    public function remove(ProductSku $productSku)
    {
        $this->cartService->remove($productSku->id);        
        return [];
    }
}
