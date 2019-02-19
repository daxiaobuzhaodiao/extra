@extends('layouts.app')
@section('title', '商品列表')

@section('content')
<div class="row">
<div class="col-lg-10 offset-lg-1">
<div class="card">
  <div class="card-body">
      <!-- 筛选组件开始 -->
      <form action="{{ route('products.index') }}" class="search-form">
        <div class="form-row">
          <div class="col-md-9">
            <div class="form-row">
              <div class="col-auto"><input type="text" class="form-control form-control-sm" name="search" placeholder="搜索"></div>
              <div class="col-auto"><button class="btn btn-primary btn-sm">搜索</button></div>
            </div>
          </div>
          <div class="col-md-3">
            <select name="order" class="form-control form-control-sm float-right">
              <option value="">排序方式</option>
              <option value="price_asc">价格从低到高</option>
              <option value="price_desc">价格从高到低</option>
              <option value="sold_count_desc">销量从高到低</option>
              <option value="sold_count_asc">销量从低到高</option>
              <option value="rating_desc">评价从高到低</option>
              <option value="rating_asc">评价从低到高</option>
            </select>
          </div>
        </div>
      </form>
      <!-- 筛选组件结束 -->
    <div class="row products-list">
      <!-- 商品列表开始 -->
      @foreach($products as $product)
        <div class="col-lg-3 col-md-4 col-sm-6 product-item">
          <div class="product-content">
            <div class="top">
              <div class="img"><img src="{{ $product->image_url }}" alt=""></div>
              <div class="price"><b>￥</b>{{ $product->price }}</div>
              <div class="title">{{ $product->title }}</div>
            </div>
            <div class="bottom">
              <div class="sold_count">销量 <span>{{ $product->sold_count }}笔</span></div>
              <div class="review_count">评价 <span>{{ $product->review_count }}</span></div>
            </div>
          </div>
        </div>
      @endforeach
      <!-- 商品列表结束 -->
    </div>
    <!-- appends() 方法接受一个 Key-Value 形式的数组作为参数，在生成分页链接的时候会把这个数组格式化成查询参数 -->
    <div class="float-right">{{ $products->appends($filters)->render() }}</div>
  </div>
</div>
</div>
</div>
@endsection

@section('customJS')
    <script>
      var filters = {!! json_encode($filters) !!}   // 注意：通过此方法直接获取后台传来的数组数据
      // console.log(filters)
      $(document).ready(function() {
        $('.search-form input[name=search]').val(filters.search)
        $('.search-form select[name=order]').val(filters.order)

        $('.search-form select[name=order]').change(function() {
          $('.search-form').submit()
        })
      })
      
    </script>
@endsection