<?php

namespace App\Admin\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use App\Http\Requests\Admin\HandleRefundRequest;

class OrdersController extends Controller   // 同样继承的 controller 类
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('订单列表')
            ->description('description')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     *   可以被解析成对象
     */
    public function show(Order $order, Content $content)
    {
        return $content
            ->header('订单详情')
            ->description('description')
            // ->body($this->detail($id));
            // body 方法可以接受 Laravel 的视图作为参数
            ->body(view('admin.orders.show', compact('order')));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('Edit')
            ->description('description')
            ->body($this->form()->edit($id));
    }


    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Order);
        
        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            // 在这里添加字段过滤器
            $filter->column(1/2, function ($filter) {
                $filter->like('no', '订单号');
                $filter->equal('refund_status', '退款状态')->select([
                    'success' => '成功',
                    'applied' => '退款中',
                    'failed' => '失败'
                ]);
            });
            $filter->column(1/2, function ($filter) {
                $filter->like('no', '订单号');
                $filter->equal('refund_status', '退款状态')->select([
                    'success' => '成功',
                    'applied' => '退款中',
                    'failed' => '失败'
                ]);
            });

        });
        // 只展示已支付的订单， 并且默认按支付时间倒序排序
        $grid->model()->whereNotNull('paid_at')->orderBy('paid_at', 'desc');

        
        
        $grid->no('订单流水号');
        // 展示关联关系的字段时， 使用 column 方法
        $grid->column('user.name', '买家');
        $grid->total_amount('总金额')->sortable();
        $grid->paid_at('支付时间')->sortable();
        $grid->ship_status('物流状态')->display(function($value) {
            return Order::$shipStatusMap[$value];
        });
        $grid->refund_status('退款状态')->display(function($value) {
            return Order::$refundStatusMap[$value];
        });
        // 禁用 创建 按钮
        $grid->disableCreateButton();
        $grid->actions(function($actions) {
            // 禁用删除和编辑按钮
            $actions->disableDelete();
            $actions->disableEdit();
        });
        $grid->tools(function($tools) {
            // 禁用批量删除按钮
            $tools->batch(function($batch) {
                $batch->disableDelete();
            });
        });
        return $grid;
    }

    // 点击发货 接口
    public function ship(Order $order, Request $request)
    {
        // 判断订单是否已经支付
        if(!$order->paid_at){
            throw new \App\Exceptions\InvalidRequestException('该订单未付款，不能发货');
        }
        // 判断该订单的发货状态是否是 未发货
        if($order->ship_status !== Order::SHIP_STATUS_PENDING) {
            throw new \App\Exceptions\InvalidRequestException('该订单已发货成功');
        }
        // 表单验证   laravel 5.5 之后 通过表单验证后返回表单提交的 键值 数组
        $data = $this->validate($request, [
            'express_company' => 'required',
            'express_no' => 'required',
        ], [], [
            'express_company' => '物流公司',
            'express_no' => '物流单号',
        ]);
        // 更改订单物流状态为 已发货
        $order->update([
            'ship_status' => Order::SHIP_STATUS_DELIVERED,
            'ship_data' => $data,  // $casts = ['ship_data' => 'json']
        ]);
        // 返回上一页
        return redirect()->back();
    }
    // 退款请求处理
    public function handleRefund(Order $order, HandleRefundRequest $request)
    {
        // 判断订单的退款状态是否为  已申请退款
        if($order->refund_status !== Order::REFUND_STATUS_APPLIED) {
            throw new \App\Exceptions\InvalidRequestException('订单退款状态有误');
        }
        // 是否同意退款
        if($request->agree) {
            // 清空拒绝退款的理由
            $extra = $order->extra ?: [];
            unset($extra['refund_disagree_reason']);
            $order->update(['extra' => $extra]);
            // 由于退款逻辑较为繁琐，所以调用我们自定义的退款方法
            $this->_refundOrder($order);
        }else{
            // 拒绝, 更新理由
            $extra = $order->extra ?: [];
            $extra['refund_disagree_reason'] = $request->reason;
            // 将拒绝理由放到 extra 字段中
            $order->update(['refund_status' => Order::REFUND_STATUS_PENDING, 'extra' => $extra]);
        }
        return $order;
    }

    // 支付宝退款逻辑
    protected function _refundOrder(Order $order)
    {
        // 判断该订单的支付方式
        switch($order->payment_method) {
            case 'wechat':
                // 微信 先留空
                break;
            case 'alipay':
                // 用 order 模型中的 定义好的方法 获得退款单号
                $refundNo = Order::getAvailableRefundNo();
                // 调用支付宝支付实例的 refund 方法
                $res = app('alipay')->refund([
                    'out_trade_no' => $order->no,   // 订单流水号
                    'refund_amount' => $order->total_amount,    // 退款金额，单位元
                    'out_request_no' => $refundNo    
                ]);
                // 根据支付宝文档，如果返回值有 sub_code 字段，说明退款失败了
                if($res->sub_code) {
                    // 将退款失败的信息保存到 extra 字段
                    $extra = $order->extra ?: [];
                    $extra['refund_failed_code'] = $res->sub_code;
                    // 标记此订单为退款失败
                    $order->update([
                        'refund_no' => $refundNo,
                        'refund_status' => Order::REFUND_STATUS_FAILED,
                        'extra' => $extra
                    ]);
                }else{
                    // 退款成功 更改退款状态保存退款单号
                    $order->update([
                        'refund_status' => Order::REFUND_STATUS_SUCCESS,
                        'refund_no' => $refundNo
                    ]);
                }
                break;
            default:
                // 原则上不会到走到这里了
                throw new \App\Exceptions\InvalidRequestException('未知的订单支付方式：'. $order->payment_method);
                break;
        }

    }
}
