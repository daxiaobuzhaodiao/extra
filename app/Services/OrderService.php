<?php
namespace App\Services;

use App\Models\ProductSku;
use App\Models\UserAddress;
use App\Exceptions\InvalidRequestException;
use App\Jobs\CloseOrderJob;
use App\Models\User;
use App\Services\CartService;

class OrderService
{
    public function store(User $user, UserAddress $address, $remark, $items)
    {
        
        // 开启数据库事物
        $order = \DB::transaction(function() use($user, $address, $remark, $items) {
            // 更新收获地址的 last_used_at 字段
            $address->update(['last_used_at' => now()]);
            
            // 创建一个 order
            $order = $user->orders()->create([
                'address' => [
                    'address' => $address->full_address,
                    'zip' => $address->zip,
                    'contact_name' => $address->contact_name,
                    'contact_phone' => $address->contact_phone
                ],
                'remark' => $remark,
                'total_amount' => 0  //暂时设置 0
            ]);
            // 创建 orderItem  以及计算订单总价
            $totalAmount = 0;
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
                    throw new InvalidRequestException('该商品库存不足');
                }
            }
            // 更新订单总金额
            $order->update(['total_amount' => $totalAmount]);
            // 从购物车删除订单
            $skuIds = collect($items)->pluck('sku_id')->toArray();
            app(CartService::class)->remove($skuIds);
            return $order;
        });
        dispatch(new CloseOrderJob($order, config('app.order_ttl')));  // dispatch全局函数
        return $order;
    }
}