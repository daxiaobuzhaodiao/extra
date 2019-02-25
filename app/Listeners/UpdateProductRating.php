<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use \App\Events\OrderReviewed;
use \App\Models\OrderItem;

class UpdateProductRating implements ShouldQueue
{
    public function handle(OrderReviewed $event)
    {
        // 预加载关系 目的获取 product
        $orderItems = $event->order->orderItems()->with(['product'])->get();
        foreach($orderItems as $item) {
            // order_items 所有的属于同一个 product
            $result = OrderItem::query()
                ->where('product_id', $item->product_id)
                ->whereHas('order', function($query) {
                    $query->whereNotNull('paid_at');
                })
                // first() 方法接受一个数组作为参数，代表此次 SQL 要查询出来的字段
                ->first([
                    \DB::raw('count(*) as review_count'), // 评价数量
                    \DB::raw('avg(rating) as rating')     // 评价平均分数
                ]);
            // 更新商品的评分和评价数量
            // dd($result);
            $item->product->update(['rating' => $result->rating, 'review_count' => $result->review_count]);
        }
    }
}
