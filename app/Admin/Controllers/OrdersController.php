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

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Order);

        $form->text('no', 'No');
        $form->number('user_id', 'User id');
        $form->textarea('address', 'Address');
        $form->decimal('total_amount', 'Total amount');
        $form->textarea('remark', 'Remark');
        $form->datetime('paid_at', 'Paid at')->default(date('Y-m-d H:i:s'));
        $form->text('payment_method', 'Payment method');
        $form->text('payment_no', 'Payment no');
        $form->text('refund_status', 'Refund status')->default('pending');
        $form->text('refund_no', 'Refund no');
        $form->switch('closed', 'Closed');
        $form->switch('reviewed', 'Reviewed');
        $form->text('ship_status', 'Ship status')->default('pending');
        $form->textarea('ship_data', 'Ship data');
        $form->textarea('extra', 'Extra');

        return $form;
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
        // 表单验证   laravel 5.5 之后 可以返回表单验证的结果
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
}
