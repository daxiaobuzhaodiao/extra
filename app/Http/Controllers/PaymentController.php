<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exceptions\InvalidRequestException;
use App\Models\Order;
use App\Events\OrderPaid;

class PaymentController extends Controller
{
    public function payByAlipay(Order $order, Request $request)
    {
        // 支付前判断
        $this->authorize('isOwnerOf', $order);
        if($order->paid_at || $order->closed) {
            throw new InvalidRequestException('订单状态有误');
        }
        // 调用支付宝网页支付
        return app('alipay')->web([
            'out_trade_no' => $order->no,   // 订单号，需要保证在商户端不能重复
            'total_amount' => $order->total_amount, // 订单金额，单位：元，支持小数点后两位
            'subject' => '支付 lara-shop 的订单：' . $order->no, // 订单标题
        ]);
    }

    // 前端回调页面
    public function alipayReturn()
    {
        // 检验提交的参数是否合法
        try {
            app('alipay')->verify();
        } catch(\Exception $e) {
            return view('pages.error', ['msg' => '数据不正确']);
        }
        return view('pages.success', ['msg' => '支付成功']);
    }

    // 服务器回调
    public function alipayNotify()
    {
        // 检验输入参数
        $data = app('alipay')->verify(); // \Log::debug('Alipay notify', $data->all());  （测试）将回调的内容写入 storage/logs/alipay.log 文件
        // 如果订单状态不是成功或者结束，则直接返回数据给支付宝 ----- 所有交易状态：https://docs.open.alipay.com/59/103672
        if(!in_array($data->trade_status, ['TRADE_SUCCESS', 'TRADE_FINISHED'])){
            return app('alipay')->success();
        }
        // 根据回调数据的单号从数据库中把这个订单找出来
        $order = Order::where('no', $data->out_trade_no)->first();

        // 更新订单信息
        $order->update([
            'paid_at' => now(),
            'payment_method' => 'alipay',
            'payment_no' => $data->trade_no
        ]);
        
        // 触发事件处理程序 增加销量 和 给用发送订单邮件
        event(new OrderPaid($order));
        return app('alipay')->success();
    }
}
