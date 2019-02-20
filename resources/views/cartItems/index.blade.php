@extends('layouts.app')
@section('title', '购物车')

@section('content')
<div class="row">
<div class="col-lg-10 offset-lg-1">
<div class="card">
  <div class="card-header">我的购物车</div>
  <div class="card-body">
    <table class="table table-striped">
      <thead>
      <tr>
        <th><input type="checkbox" id="select-all"></th>
        <th>商品信息</th>
        <th>单价</th>
        <th>数量</th>
        <th>操作</th>
      </tr>
      </thead>
      <tbody class="product_list">
      @foreach($cartItems as $item)
        <!-- <tr> 标签的 data-id 属性的值，这是为了之后删除商品或者提交订单时能够取得对应的 ID -->
        <tr data-id="{{ $item->productSku->id }}">
          <td>
            <!-- 虽然已经被加入到了购物车，但是经过一段时间后该商品可能会被下架，所以做一个判断，如果已下架则该商品不能被选中（disabled） -->
            <input type="checkbox" name="select" value="{{ $item->productSku->id }}" {{ $item->productSku->product->on_sale ? 'checked' : 'disabled' }}>
          </td>
          <td class="product_info">
            <div class="preview">
              <a target="_blank" href="{{ route('products.show', [$item->productSku->product_id]) }}">
                <img src="{{ $item->productSku->product->image_url }}">
              </a>
            </div>
            
            <div @if(!$item->productSku->product->on_sale) class="not_on_sale" @endif>
              <span class="product_title">
                <a target="_blank" href="{{ route('products.show', [$item->productSku->product_id]) }}">{{ $item->productSku->product->title }}</a>
              </span>
              <span class="sku_title">{{ $item->productSku->title }}</span>
              <!-- 如果已经下架输出提醒信息 -->
              @if(!$item->productSku->product->on_sale)
                <span class="warning">该商品已下架</span>
              @endif
            </div>
          </td>
          <td><span class="price">￥{{ $item->productSku->price }}</span></td>
          <td>
            <input type="text" class="form-control form-control-sm amount" @if(!$item->productSku->product->on_sale) disabled @endif name="amount" value="{{ $item->amount }}">
          </td>
          <td>
            <button class="btn btn-sm btn-danger btn-remove">移除</button>
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>
</div>
</div>
</div>
@endsection

@section('customJS')
    <script>
        $(document).ready(function() {

            // 删除
            $('.btn-remove').click(function() {
                // closest() --------  https://developer.mozilla.org/en-US/docs/Web/API/Element/closest
                // closest() --------  http://jquery.cuishifeng.cn/closest.html
                var productId = $(this).closest('tr').data('id')
                // 弹窗提醒
                Swal.fire({
                    title: '确定要移除么？',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: '确定',
                    cancelButtonText: '取消'
                }).then((result) => {
                    if (result.value) {
                        // 点击了确定
                        axios.delete('/carts/' + productId).then((res) => {
                            Swal.fire('移除成功', '', 'success').then(() => [
                                $(this).closest('tr').remove() // 另一种方式 location.reload()
                            ])
                        }).then((err) => {
                            console.log(err.data.response)
                            // 服务器出错了
                        })
                    }else{
                        // 点击了取消
                    }
                })
            })

            // 全选 反选
            $('#select-all').change(function() {
                // element.prop() ------ http://jquery.cuishifeng.cn/prop.html
                var checked = $(this).prop('checked')
                $('input[name=select][type=checkbox]:not([disabled])').prop('checked', checked) // 可以批量赋值, 也可以 .each(function () {})
            })
        })
    </script>
@endsection