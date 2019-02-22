<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Order;

class CloseOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;

    public function __construct(Order $order, $delay)
    {
        $this->order = $order;
        // 设置延迟的时间，delay() 方法的参数代表多少秒之后执行
        $this->delay($delay);
    }

    public function handle()
    {
        if($this->order->paid_at){
            return;
        }
        
        // 通过事务执行 sql
        \DB::transaction(function() {
            // 将订单的 close 字段改为 true  即为关闭订单
            $this->order->update(['closed' => true]);
            // 循环遍历此订单中的 sku 得到购买数量，将这些数量恢复到 sku 的库存中
            foreach($this->order->orderItems as $orderItem) {
                $orderItem->productSku->addStock($orderItem->amount);
            }
        });
    }
}
