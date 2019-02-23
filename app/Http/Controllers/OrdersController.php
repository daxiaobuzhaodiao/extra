<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Services\OrderService;
use App\Models\UserAddress;
use App\Http\Requests\ReviewRequest;

class OrdersController extends Controller
{
    protected $orderService;
    function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }
    // 创建订单
    public function store(OrderRequest $request)
    {
        $user = $request->user();
        $items = $request->items;
        $address = UserAddress::findOrFail($request->address_id);
        $remark = $request->remark;
        $order = $this->orderService->store($user, $address, $remark, $items);
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

    // 确认收获
    public function received(Order $order, Request $request)
    {
        $this->authorize('isOwnerOf', $order);
        // 判断订单状态是否为已发货
        if($order->ship_status !== Order::SHIP_STATUS_DELIVERED) {
            throw new \App\Exceptions\InvalidRequestException('发货状态不正确');
        }
        $order->update(['ship_status' => Order::SHIP_STATUS_RECEIVED]);
        // 返回原页面
        return $order;
    }

    // 返回某个订单下面的 sku 的评价内容
    public function review(Order $order)
    {
        $this->authorize('isOwnerOf', $order);
        // 判断订单是否 已支付
        if(!$order->paid_at){
            throw new \App\Exceptions\InvalidRequestException('该订单未支付，不可评价');
        }
        // 加载关系
        $order->load(['orderItems.product', 'orderItems.productSku']);
        return view('orders.review', compact('order'));
    }
    // 评价某个订单中 sku
    public function sendReview(Order $order, ReviewRequest $request)
    {
        // 判断该订单是否 已支付
        if(!$order->paid_at) {
            throw new \App\Exceptions\InvalidRequestException('该订单未支付，不可评价');
        }
        // 判断该订单是否 已评价
        if($order->reviewed) {
            throw new \App\Exceptions\InvalidRequestException('该订单已评价，不可重新评价');
        }
        $reviews = $request->reviews;
        // 开启事务
        \DB::transaction(function() use($reviews, $order) {
            foreach($reviews as $review) {
                $orderItem = $order->orderItems()->findOrFail($review['id']);
                $orderItem->update([
                    'rating' => $review['rating'],
                    'review' => $review['review'],
                    'reviewed_at' => now()
                ]);
            }
            // 标记订单为已评价
            $order->update(['reviewed' => true]);
            event(new \App\Events\OrderReviewed($order)); // 必须在事务中触发事件  更新订单 评分 和 评价数量
        });

        return redirect()->back();
    }
}
