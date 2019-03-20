@extends('layouts.app')
@section('title', '购物车')

@section('content')
<div class="row">
<div class="col-lg-10 offset-lg-1">
<div class="card">
  <div class="card-header bg-transparent">我的购物车</div>
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
      @foreach($carts as $item)
        <!-- <tr> 标签的 data-id 属性的值，这是为了之后删除商品或者提交订单时能够取得对应的 ID -->
        <tr data-id="{{ $item->productSku->id }}">
          <td>
            <!-- 虽然已经被加入到了购物车，但是经过一段时间后该商品可能会被下架，所以做一个判断，如果已下架则该商品不能被选中（disabled） -->
            <input type="checkbox" name="select" value="{{ $item->productSku->id }}" {{ $item->productSku->product->on_sale ? 'checked' : 'disabled' }}>
          </td>
          <td class="product_info">
            <div class="preview">
              <!-- 商品图片 -->
              <a target="_blank" href="{{ route('products.show', [$item->productSku->product_id]) }}">
                <img src="{{ $item->productSku->product->image_url }}">
              </a>
            </div>
            
            <div @if(!$item->productSku->product->on_sale) class="not_on_sale" @endif>
              <!-- 商品名称 -->
              <span class="product_title">
                <a target="_blank" href="{{ route('products.show', [$item->productSku->product_id]) }}">{{ $item->productSku->product->title }}</a>
              </span>
              <!-- 商品 sku 名称 -->
              <span class="sku_title">{{ $item->productSku->title }}</span>
              <!-- 如果已经下架输出提醒信息 -->
              @if(!$item->productSku->product->on_sale)
                <span class="warning">该商品已下架</span>
              @endif
            </div>
          </td>
          <!-- sku 单价 -->
          <td><span class="price">￥{{ $item->productSku->price }}</span></td>
          <td>
            <!-- 将要购买的 sku 数量 -->
            <input type="text" class="form-control form-control-sm amount" @if(!$item->productSku->product->on_sale) disabled @endif name="amount" value="{{ $item->amount }}">
          </td>
          <td>
            <button class="btn btn-sm btn-danger btn-remove">移除</button>
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>
    <!-- 地址选择框 / 备注框 / 优惠券检查 开始 -->
    <div>
      <form class="form-horizontal" role="form" id="order-form">
        <div class="form-group row">
          <label class="col-form-label col-sm-3 text-md-right">选择收货地址</label>
          <div class="col-sm-9 col-md-7">
            <select class="form-control" name="address">
              @foreach($addresses as $address)
                <option value="{{ $address->id }}">{{ $address->full_address }} {{ $address->contact_name }} {{ $address->contact_phone }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <!-- 订单备注 -->
        <div class="form-group row">
          <label class="col-form-label col-sm-3 text-md-right">备注</label>
          <div class="col-sm-9 col-md-7">
            <textarea name="remark" class="form-control" rows="3"></textarea>
          </div>
        </div>
        <!-- 优惠码 开始 -->
        <div class="form-group row">
          <label class="col-form-label col-sm-3 text-md-right">优惠码</label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="coupon_code">
            <span class="form-text text-muted" id="coupon_desc"></span>
          </div>
          <div class="col-sm-3">
            <button type="button" class="btn btn-success" id="btn-check-coupon">检查</button>
            <button type="button" class="btn btn-danger" style="display: none;" id="btn-cancel-coupon">取消</button>
          </div>
        </div>
        <!-- 优惠码结束 -->
        <div class="form-group">
          <div class="offset-sm-3 col-sm-3">
            <button type="button" class="btn btn-primary btn-create-order">提交订单</button>
          </div>
        </div>
      </form>
    </div>
    <!-- 地址选择框 / 备注框 / 检查优惠券 结束 -->
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
                            console.log(err.response.data)
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
                var checked = $(this).prop('checked')   // 返回 true / false
                $('input[name=select][type=checkbox]:not([disabled])').prop('checked', checked) // 可以批量赋值, 也可以 .each(function () {})
            })

            // 提交订单
            $('.btn-create-order').click(function() {
              // 构建请求参数
              var requestData = {
                address_id: $('select[name=address]').val(),
                items: [],
                remark: $('textarea[name=remark]').val(),
                coupon_code: $('input[name=coupon_code]').val() // 从优惠 input 中获取 优惠吗1
              }
              // 遍历 <table> 标签内所有带有 data-id 属性的 <tr> 标签，也就是每一个购物车中的商品 SKU
              $('table tr[data-id]').each(function() {
                // 确定当前的 sku 是否被选中 并且 没有被禁用
                var $checkbox = $(this).find('input[type=checkbox][name=select]')
                if($checkbox.prop('disabled') || !$checkbox.prop('checked')){
                  return
                }
                // 获取当前 sku 数量
                var $input = $(this).find('input[name=amount]')
                // 如果数量的值为 0， 或者不是一个 数字 则也跳过
                if($input.val() == 0 || isNaN($input.val())){
                  return
                }
                // 我们只需要 sku_id 和 amount 参数 将他们放进 requestData 数组
                requestData.items.push({
                  sku_id: $(this).data('id'),
                  amount: $input.val()
                })
              })
              // console.log(requestData)
              axios.post('{{ route('orders.store') }}', requestData).then((res) => {
                console.log(res.response)
                Swal.fire('订单提交成功', '', 'success').then(() => {
                  location.href = '/orders/' + res.data.id // 跳转到 订单详情页
                })
              }).catch((err) => {
                if(err.response.status === 422) {
                  // 表单验证错误
                  var html = '<div>'
                  _.each(err.response.data.errors, function(error) {
                    _.each(error, function(err) {
                      html += err + '<br/>'
                    })
                  })
                  html += '</div>'
                  // 将错误信息输出到弹框
                  Swal.fire(html, '', 'error')
                }else if(err.response.status === 403){
                  console.log(err.response.data)
                  Swal.fire(err.response.data.msg, '', 'error')
                }else{
                  console.log(err.response)
                  Swal.fire('未知错误', '', 'error')
                }
              })
            })

            // 检查优惠券是否有效
            $('#btn-check-coupon').click(function() {
              // 获取填入的券码
              var code = $('input[name=coupon_code]').val()
              if(!code) {
                Swal.fire('请输入优惠券','', 'warning')
                return
              }
              // 调用检查接口
              axios.get('/coupons/' + encodeURIComponent(code))
                .then((res) => {
                  // console.log(res.data.record.code)
                  $('#coupon_desc').text(res.data.record.description)
                  $('input[name=coupon_code]').prop('readonly', true)
                  $('#btn-check-coupon').hide()
                  $('#btn-cancel-coupon').show()
                })
                .catch((err) => {
                  if(err.response.status === 404){
                    Swal.fire('该券不存在', '', 'error')
                  }else if(err.response.status === 403){
                    Swal.fire(err.response.data.msg, '', 'error')
                  }else {
                    Swal.fire('服务器错误,请联系客服', '', 'error')
                  }
                })
            })

            // 取消使用优惠券
            $('#btn-cancel-coupon').click(function() {
              $('#coupon_desc').text('')
              $('input[name=coupon_code]').prop('readonly', false)
              $('input[name=coupon_code]').val('')
              $('#btn-cancel-coupon').hide()
              $('#btn-check-coupon').show()
            })
        })
    </script>
@endsection