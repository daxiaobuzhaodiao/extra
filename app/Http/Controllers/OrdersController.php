<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Services\OrderService;
use App\Models\UserAddress;

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
}
