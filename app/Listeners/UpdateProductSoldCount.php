<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\OrderPaid;
use App\Models\OrderItem;

// implements ShouldQueue 代表异步监听器
class UpdateProductSoldCount implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(OrderPaid $event)
    {
        $order = $event->order;
        // 预加载关系
        $order->load('orderItems.product');
        foreach($order->orderItems as $item) {
            $product = $item->product;
            $soldCount = OrderItem::query()
                ->where('product_id', $product->id)
                ->whereHas('order', function($query) {
                    $query->whereNotNull('paid_at');
                })
                ->sum('amount');
            // 更新销量
            $product->update(['sold_count' => $soldCount]);
        }
    }
}
