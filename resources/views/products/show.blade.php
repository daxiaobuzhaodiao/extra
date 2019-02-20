@extends('layouts.app')
@section('title', $product->title)

@section('content')
<div class="row">
<div class="col-lg-10 offset-lg-1">
<div class="card">
  <div class="card-body product-info">
    <div class="row">
      <div class="col-5">
        <img class="cover" src="{{ $product->image_url }}" alt="">
      </div>
      <div class="col-7">
        <div class="title">{{ $product->title }}</div>
        <div class="price"><label>价格</label><em>￥</em><span>{{ $product->price }}</span></div>
        <div class="sales_and_reviews">
          <div class="sold_count">累计销量 <span class="count">{{ $product->sold_count }}</span></div>
          <div class="review_count">累计评价 <span class="count">{{ $product->review_count }}</span></div>
          <div class="rating" title="评分 {{ $product->rating }}">评分 <span class="count">{{ str_repeat('★', floor($product->rating)) }}{{ str_repeat('☆', 5 - floor($product->rating)) }}</span></div>
        </div>
        <div class="skus">
          <label>选择</label>
          <div class="btn-group btn-group-toggle" data-toggle="buttons">
            <!-- skus list start -->
            @foreach($product->skus as $sku)
                <label
                    class="btn sku-btn"
                    data-price="{{ $sku->price }}"
                    data-stock="{{ $sku->stock }}"
                    data-toggle="tooltip"
                    title="{{ $sku->description }}"
                    data-placement="bottom">
                <input type="radio" name="skus" autocomplete="off" value="{{ $sku->id }}"> {{ $sku->title }}
                </label>
            @endforeach
            <!-- skus list end -->
          </div>
        </div>
        <div class="cart_amount"><label>数量</label>
            <input type="text" class="form-control form-control-sm" value="1"><span>件</span>
            <!-- 显示库存，点击了sku之后会显示 -->
            <span class="stock"></span>
        </div>
        <div class="buttons">
          @if($favored)
            <button class="btn btn-danger btn-disfavor">取消收藏</button>
          @else
            <button class="btn btn-success btn-favor">点击收藏</button>
          @endif
          <button class="btn btn-primary btn-add-to-cart">加入购物车</button>
        </div>
      </div>
    </div>
    <div class="product-detail">
      <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
          <a class="nav-link active" href="#product-detail-tab" aria-controls="product-detail-tab" role="tab" data-toggle="tab" aria-selected="true">商品详情</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#product-reviews-tab" aria-controls="product-reviews-tab" role="tab" data-toggle="tab" aria-selected="false">用户评价</a>
        </li>
      </ul>
      <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="product-detail-tab">
          {!! $product->description !!}
        </div>
        <div role="tabpanel" class="tab-pane" id="product-reviews-tab">
        </div>
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
            // 显示 sku 价格和库存
            // 工具提示 ----- https://getbootstrap.com/docs/4.0/components/tooltips/#example-enable-tooltips-everywhere
            $('[data-toggle="tooltip"]').tooltip({trigger: 'hover'})
            $('.sku-btn').click(function(){
                $('.product-info .price span').text($(this).data('price'))
                $('.product-info .stock').text('库存： ' + $(this).data('stock') + ' 件')
            })
            // 收藏
            $('.btn-favor').click(function() {
              axios.post('{{ route('products.favor', $product->id) }}').then((res) => {
                console.log(res.response)
                Swal.fire('收藏成功', '', 'success').then(() => {
                  location.reload()
                })
              }).catch((err) => {
                console.log(err.response)
                if(err.response && err.response.status === 401){
                  Swal.fire('请先登录', '', 'error').then(() => {
                    // 跳转到登录页面
                    location.href = '/login';
                  })
                }else if(err.response && err.response.status === 403 ){
                  Swal.fire('请验证邮箱', '', 'error')
                }else if(err.response && err.response.data.msg){
                  Swal.fire(err.response.data.msg, '', 'error')
                }else {
                  Swal.fire('系统错误', '', 'error')
                }
              })
            })
            // 取消收藏
            $('.btn-disfavor').click(function() {
              axios.delete('{{ route('products.disfavor', $product->id) }}').then((res) => {
                Swal.fire('取消成功', '', 'success').then(() => {
                  location.reload()
                })
              }).catch((err) => {
                console.log(err)
              })
            })

            // 添加购物车
            $('.btn-add-to-cart').click(function() {
              // btn-group ------- https://getbootstrap.com/docs/4.0/components/buttons/#checkbox-and-radio-buttons
              axios.post('{{ route('carts.add') }}', {
                'sku_id': $('label.active input[name=skus]').val(),
                'amount': $('.cart_amount input').val()
              }).then((res) => {
                // 添加成功
                Swal.fire('添加购物车成功', '', 'success').then(() => {
                  location.href = '{{ route('carts.index') }}'
                })
              }).catch((err) => {
                console.log(err.response)
                // 401 未授权，引导跳转到登录页
                if(err.response.status === 401){
                  Swal.fire('请先登录', '', 'error').then(() => {
                    location.href = '{{ route('login') }}'
                  }) 
                // 403 权限，引导跳转到邮箱验证页
                }else if(err.response.status === 403){
                  Swal.fire('请先验证邮箱', '', 'error').then(() => {
                    location.href = '{{ route('verification.notice') }}'
                  })
                // 422 表单验证错误，返回错误信息
                }else if(err.response.status === 422){
                  var html = '<div>'
                  _.each(err.response.data.errors, function (err) {
                    _.each(err, function (err) {
                      html += err + '<br/>'
                    })
                  })
                  html += '</div>'
                  Swal.fire(html, '', 'error')
                }else{
                  Swal.fire('系统错误', '', 'error')
                }
              })
            })
        })
    </script>
@endsection