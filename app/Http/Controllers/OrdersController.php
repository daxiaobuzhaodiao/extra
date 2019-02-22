<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\OrderRequest;
use App\Models\UserAddress;
use App\Models\Order;
use App\Models\CartItem;
use App\Models\ProductSku;
use App\Exceptions\InvalidRequestException;
use App\Jobs\CloseOrderJob;

class OrdersController extends Controller
{
    // 创建订单
    public function store(OrderRequest $request)
    {
        $user = $request->user();
        // 开启数据库事物
        $order = \DB::transaction(function() use($user, $request) {
            // 更新收获地址的 last_used_at 字段
            $address = UserAddress::findOrFail($request->address_id);
            $address->update(['last_used_at' => now()]);
            
            // 创建一个 order
            $order = $user->orders()->create([
                'address' => [
                    'address' => $address->full_address,
                    'zip' => $address->zip,
                    'contact_name' => $address->contact_name,
                    'contact_phone' => $address->contact_phone
                ],
                'remark' => $request->remark,
                'total_amount' => 0  //暂时设置 0
            ]);
            // 创建 orderItem  以及计算订单总价
            $totalAmount = 0;
            $items = $request->items;
            foreach($items as $item) {
                $productSku = ProductSku::findOrfail($item['sku_id']); // 为了获取单价 需要获取 sku
                $orderItem = $order->orderItems()->make([
                    'amount' => $item['amount'],
                    'price' => $productSku->price,
                ]);

                // 关联到 product 和 productSku  assciate() 参数可以是 id 也可以是 对象
                $orderItem->product()->associate($productSku->product_id);
                $orderItem->productSku()->associate($productSku);
                $orderItem->save();
                // 计算总价
                $totalAmount += $item['amount'] * $productSku->price;
                // 减库存
                if($productSku->decreaseStock($item['amount']) < 0) {
                    // 因为事务回滚机制 ，如果抛出异常，则 order 和 orderItem 数据不会被创建
                    throw new InvalidRequestException('该商品库存不足');
                }
            }
            // 更新订单总金额
            $order->update(['total_amount' => $totalAmount]);
            // 从购物车删除订单
            $skuIds = collect($items)->pluck('sku_id');
            $user->cartItems()->whereIn('product_sku_id', $skuIds)->delete();
            return $order;
        });
        $this->dispatch(new CloseOrderJob($order, config('app.order_ttl'))); // 加入队列
        return $order;
    }

    // 我的订单列表
    public function index(Request $request)
    {
        $orders = Order::query()->with(['orderItems.product', 'orderItems.productSku'])
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(5);
        return view('orders.index', ['orders' => $orders]);
    }

    // 订单详情
    public function show(Order $order)
    {
        $this->authorize('isOwnerOf', $order);
        return view('orders.show', ['order' => $order->load(['orderItems.product', 'orderItems.productSku'])]);
    }
}
