<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');  // 欢迎页
   	$router->get('users', 'UsersController@index')->name('admin.users.index'); // 用户列表
    $router->resource('products', 'ProductsController');    // 商品列表
    $router->get('orders', 'OrdersController@index')->name('admin.orders.index');   // 订单列表   
    $router->get('orders/{order}', 'OrdersController@show')->name('admin.orders.show');
    
});
