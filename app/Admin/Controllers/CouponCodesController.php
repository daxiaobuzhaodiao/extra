<?php

namespace App\Admin\Controllers;

use App\Models\CouponCode;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class CouponCodesController extends Controller
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
            ->header('优惠券列表')
            ->description('description')
            ->body($this->grid());
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
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('创建优惠券')
            ->description('description')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new CouponCode);

        // 默认按照创建时间倒叙排序
        $grid->model()->orderBy('created_at', 'desc');
        $grid->id('Id')->sortable();
        $grid->name('名称');
        $grid->code('券码');
        $grid->type('类型')->display(function($value) {
            return CouponCode::$typeMap[$value];
        });
        // $grid->description('描述');
        // 根据不同的类型显示
        // $grid->value('折扣')->display(function($value) {
        //     return $this->type === CouponCode::TYPE_FIXED ? '￥'.$value : $value.'%';
        // });
        // $grid->column('usage', '用量')->display(function() {
        //     return "{$this->used} / {$this->total}";
        // });

        $grid->column('description', '折扣描述')->display(function() {
            $str = '';
            if($this->min_amount > 0) {
                $str = str_replace('.00', '', $this->min_amount);
            }
            if($this->type === CouponCode::TYPE_PERCENT) {
                return '满'. $str . '优惠' . str_replace('.00','',$this->value). '%';
            }else{
                return '满'. $str . '减' . str_replace('.00', '', $this->value);
            }
        });
        $grid->usag('用量');
        // $grid->min_amount('最低金额');
        $grid->enabled('启用')->display(function($value) {
            return $value ? '是' : '否';
        });
        $grid->created_at('创建时间');
        $grid->actions(function ($actions) {
            $actions->disableView();
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
        $form = new Form(new CouponCode);

        $form->text('name', '名称')->rules('required');
        // 允许用户不填，下方使用 模型中 定义的方法
        $form->text('code', '优惠码')->placeholder('可不填，系统将会生成随机16位券码')->rules(function(Form $form) {
            if($id = $form->model()->id){
                return 'nullable|unique:coupon_codes,code,'.$id.',id';
            }else{
                return 'nullable|unique:coupon_codes';
            }
        });
        $form->radio('type', '类型')->options(CouponCode::$typeMap)->rules('required');
        // 我们的校验规则是一个匿名函数，当我们的校验规则比较复杂，或者需要根据用户提交的其他字段来判断时就可以用匿名函数的方式来定义校验规则。
        $form->number('value', '折扣')->rules(function (From $form) {
            if($form->model()->type === 0) {
                // 如果选择了百分比折扣类型，那么折扣范围只能是 1 ~ 99
                return 'required|numeric|between:1,99';
            }else{
                // 否则只要大于 0.01 就行 
                return 'required|numeric|min:0.01';
            }
        });
        $form->number('total', '总量')->rules('required|numeric|min:0');
        $form->number('min_amount', '最低金额')->rules('required|numeric|min:0');
        $form->switch('enabled', '启用');
        $form->datetime('not_before', '开始时间')->default(date('Y-m-d H:i:s'));
        $form->datetime('not_after', '结束时间')->default(date('Y-m-d H:i:s'));
        // 类似于 observer 的 saving 观察器
        $form->saving(function (Form $form) {
            if (!$form->code) {
                $form->code = CouponCode::createAvailableCode();
            }
        });
        $form->footer(function($footer) {
            // 去掉`查看`checkbox
            $footer->disableViewCheck();
            // 去掉`继续编辑`checkbox
            $footer->disableEditingCheck();
        });
     
        return $form;
    }
}
