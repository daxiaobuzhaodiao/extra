<?php
namespace App\Services;

use App\Models\User;
use App\Models\CouponCode;
use App\Models\ProductSku;
use App\Jobs\CloseOrderJob;
use App\Models\UserAddress;
use App\Services\CartService;
use App\Exceptions\InvalidRequestException;
use App\Exceptions\CouponCodeUnavailableException;

class OrderService
{
    // 注意： address 必须从控制器中传对象过来，如果传递一个 id 过来，不会被解析成对象
    // 下面的 UserAddress $address 中的 UserAddress 可以省略不写，因为传递来的就是对象，不需要解析
    public function store(User $user, UserAddress $address, $remark, $items, $coupon = null)
    {
        // 如果传入了优惠券，则先检查是否可用
        if($coupon) {
            $coupon->checkAvailable($user);
        }
        // 开启数据库事物
        $order = \DB::transaction(function() use($user, $address, $remark, $items, $coupon) {
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
            if ($coupon) {
                // 总金额已经计算出来了，检查是否符合优惠券规则 （是否高于最低金额）
                $coupon->checkAvailable($user, $totalAmount);
                // 把订单金额修改为优惠后的金额
                $totalAmount = $coupon->getAdjustedPrice($totalAmount);
                // 将订单与优惠券关联
                $order->couponCode()->associate($coupon);
                // 增加优惠券的用量，需判断返回值
                if ($coupon->changeUsed() <= 0) {
                    throw new CouponCodeUnavailableException('该优惠券已被兑完');
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