@extends('layouts.app')
@section('title', '收货地址列表')

@section('content')
  <div class="row">
    <div class="col-md-10 offset-md-1">
      <div class="card panel-default">
        <div class="card-header bg-transparent">
          收货地址列表
          <a href="{{ route('user_addresses.create') }}" class="float-right">新增收获地址</a>
        </div>
        <div class="card-body">
          <table class="table table-bordered table-striped">
            <thead>
            <tr>
              <th>收货人</th>
              <th>地址</th>
              <th>邮编</th>
              <th>电话</th>
              <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($addresses as $address)
              <tr>
                <td>{{ $address->contact_name }}</td>
                <td>{{ $address->full_address }}</td>
                <td>{{ $address->zip }}</td>
                <td>{{ $address->contact_phone }}</td>
                <td>
                  <a class="btn btn-primary btn-sm edit" href="{{ route('user_addresses.edit', $address->id) }}">修改</a>
                  <button class="btn btn-danger btn-sm del" data-id="{{ $address->id }}">删除</button>
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
    $(document).ready(function () {
      $('.del').click(function () {
        var id = $(this).data('id');
        Swal.fire({
          title: '您确定要删除么？？',
          type: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#3085d6',
          confirmButtonText: '删除',
          cancelButtonText: '取消'
        }).then((result) => {
          if (result.value) {
            axios.delete('/user_addresses/' + id).then((res)=>{
              if(res.status === 200){
                Swal.fire(
                  '删除成功',
                  '',
                  'success'
                )
                location.reload()
              }else{
                Swal.fire(
                  '服务器错误',
                  '',
                  'danger'
                )
              }
            })
          }
        })
      })
    })
  
  </script>

@endsection