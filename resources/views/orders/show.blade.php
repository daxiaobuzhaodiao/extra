@extends('layouts.app')

@section('title', '订单详情')

@section('content')
    <div class="row">
        <div class="col-lg-10 offset-lg-1">
            <div class="card">
                <div class="card-header bg-transparent">
                    <h4>订单详情</h4>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                        <tr>
                        <th>商品信息</th>
                        <th class="text-center">单价</th>
                        <th class="text-center">数量</th>
                        <th class="text-right item-amount">小计</th>
                        </tr>
                        </thead>
                        @foreach($order->orderItems as $index => $item)
                        <tr>
                            <td class="product-info">
                            <div class="preview">
                                <a target="_blank" href="{{ route('products.show', [$item->product_id]) }}">
                                <img src="{{ $item->product->image_url }}">
                                </a>
                            </div>
                            <div>
                                <span class="product-title">
                                    <a target="_blank" href="{{ route('products.show', [$item->product_id]) }}">{{ $item->product->title }}</a>
                                </span>
                                <span class="sku-title">{{ $item->productSku->title }}</span>
                            </div>
                            </td>
                            <td class="sku-price text-center vertical-middle">￥{{ $item->price }}</td>
                            <td class="sku-amount text-center vertical-middle">{{ $item->amount }}</td>
                            <td class="item-amount text-right vertical-middle">￥{{ number_format($item->price * $item->amount, 2, '.', '') }}</td>
                        </tr>
                        @endforeach
                        <tr><td colspan="4"></td></tr>
                    </table>
                    <div class="order-bottom">
                        <div class="order-info">
                            <div class="line"><div class="line-label">收货地址：</div><div class="line-value">{{ join(' ', $order->address) }}</div></div>
                            <div class="line"><div class="line-label">订单备注：</div><div class="line-value">{{ $order->remark ?: '-' }}</div></div>
                            <div class="line"><div class="line-label">订单编号：</div><div class="line-value">{{ $order->no }}</div></div>
                             <!-- 输出物流状态 -->
                            <div class="line">
                                <div class="line-label">物流状态：</div>
                                <div class="line-value">{{ \App\Models\Order::$shipStatusMap[$order->ship_status] }}</div>
                            </div>
                            <!-- 如果有物流信息则展示 -->
                            @if($order->ship_data)
                            <div class="line">
                                <div class="line-label">物流信息：</div>
                                <div class="line-value">{{ $order->ship_data['express_company'] }} {{ $order->ship_data['express_no'] }}</div>
                            </div>
                            @endif
                             <!-- 订单已支付，且退款状态不是未退款时展示 退款信息 -->
                            @if($order->paid_at && $order->refund_status !== \App\Models\Order::REFUND_STATUS_PENDING)
                                <div class="line">
                                    <div class="line-label">退款状态：</div>
                                    <div class="line-value">{{ \App\Models\Order::$refundStatusMap[$order->refund_status] }}</div>
                                </div>
                                <div class="line">
                                    <div class="line-label">退款理由：</div>
                                    <div class="line-value">{{ $order->extra['refund_reason'] }}</div>
                                </div>
                            @endif
                        </div>
                        <div class="order-summary text-right">
                             <!-- 展示优惠信息开始 -->
                            @if($order->couponCode)
                                <div class="text-primary">
                                    <span>优惠信息：</span>
                                    <div class="value">{{ $order->couponCode->description }}</div>
                                </div>
                            @endif
                            <!-- 展示优惠信息结束 -->
                            <div class="total-amount">
                                <span>订单总价：</span>
                                <div class="value">￥{{ $order->total_amount }}</div>
                            </div>
                            <div>
                                <span>订单状态：</span>
                                <div class="value">
                                    @if($order->paid_at)
                                        @if($order->refund_status === \App\Models\Order::REFUND_STATUS_PENDING)
                                        已支付
                                        @else
                                        {{ \App\Models\Order::$refundStatusMap[$order->refund_status] }}
                                        @endif
                                    @elseif($order->closed)
                                        已关闭
                                    @else
                                        未支付
                                    @endif
                                    <!-- 如果订单的发货状态为已发货则展示确认收货按钮 -->
                                    @if($order->ship_status === \App\Models\Order::SHIP_STATUS_DELIVERED)
                                        <div class="receive-button">
                                            <button type="button" id="btn-receive" class="btn btn-sm btn-success">确认收货</button>
                                        </div>
                                    @endif
                                   
                                </div>
                            </div>
                            <!-- 如果退款申请被拒绝 -->
                            @if(isset($order->extra['refund_disagree_reason']))
                            <div>
                                <span>拒绝退款理由：</span>
                                <div class="value">{{ $order->extra['refund_disagree_reason'] }}</div>
                            </div>
                            @endif
                             <!-- 订单已支付，且退款状态是未退款时展示申请退款按钮 -->
                            @if($order->paid_at && $order->refund_status === \App\Models\Order::REFUND_STATUS_PENDING)
                                <div class="refund-button">
                                    <button class="btn btn-sm btn-danger" id="btn-apply-refund">申请退款</button>
                                </div>
                            @endif
                            <!-- 支付按钮开始 -->
                            @if(!$order->paid_at && !$order->closed)
                                <div class="payment-buttons">
                                    <a class="btn btn-primary btn-sm" href="{{ route('payment.alipay', ['order' => $order->id]) }}">支付宝支付</a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('customJS')
    <script>
        $(document).ready(function() {
            // 确认收货
            $('#btn-receive').click(function() {
                Swal.fire({
                    title: '确认收获？',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: '确定',
                    cancelButtonText: '取消'
                }).then((result) => {
                    if (result.value) {
                        axios.post('{{ route('orders.received', $order->id) }}').then((res) => {
                            Swal.fire('操作成功', '', 'success').then(() => {
                                location.reload()
                            })
                        }).catch((err) => {
                            console.log(err.response)
                        })
                        
                    }else{
                        // 点击了取消
                    }
                })
            })

            // 申请退款
            $('#btn-apply-refund').click(function() {
                Swal.fire({
                    title: '请输入退款理由',
                    input: 'text',
                    inputAttributes: {
                        autocapitalize: 'off'
                    },
                    showCancelButton: true,
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    showLoaderOnConfirm: true,
                    preConfirm: (data) => {
                        // data 就是input的值
                        axios.post('{{ route('orders.apply_refund', $order->id) }}', {reason: data})
                        .then((res)=>{
                            // 请求成功
                            Swal.fire('申请成功', '', 'success').then(()=>{
                                location.reload()
                            })
                        }).catch((err)=>{
                            // 请求失败
                            console.log(err.response)
                            Swal.fire(err.response.data.errors.reason[0], '', 'error')
                        })
                    }
                }).then(()=>{
                    // 取消了
                    // alert(22);
                })
            })
        })
    </script>
@endsection