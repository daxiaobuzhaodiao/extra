<?php

Auth::routes(['verify' => true]);

Route::get('/', 'ProductsController@index')->name('products.index');
Route::get('test', 'HomeController@index')->name('test'); // 各种调试

Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::resource('user_addresses', 'UserAddressesController');   // 收获地址
    Route::post('products/{product}/favor', 'ProductsController@favor')->name('products.favor');    // 点击收藏
    Route::delete('products/{product}/disfavor', 'ProductsController@disfavor')->name('products.disfavor'); // 取消收藏
    Route::get('products/favorites', 'ProductsController@favorites')->name('products.favorites');   // 收藏列表
    Route::post('carts', 'CartsController@add')->name('carts.add'); // 添加购物车
    Route::get('carts', 'CartsController@index')->name('carts.index');  // 购物车列表
    Route::delete('carts/{productSku}', 'CartsController@remove')->name('carts.remove'); // 删除购物车
    Route::post('orders', 'OrdersController@store')->name('orders.store');  // 创建订单
    Route::get('orders', 'OrdersController@index')->name('orders.index');   // 订单列表
    Route::get('orders/{order}', 'OrdersController@show')->name('orders.show'); // 订单详情
    Route::get('payment/{order}/alipay', 'PaymentController@payByAlipay')->name('payment.alipay'); // 支付宝支付
    Route::get('payment/alipay/return', 'PaymentController@alipayReturn')->name('payment.alipay.return'); // 支付宝前端页面回掉
    Route::post('orders/{order}/received', 'OrdersController@received')->name('orders.received');   // 确认收获
    Route::get('orders/{order}/review', 'OrdersController@review')->name('orders.review.show'); // 显示某个订单的评价内容
    Route::post('orders/{order}/review', 'OrdersController@sendReview')->name('orders.review.store'); // 评价某个订单
});

Route::post('payment/alipay/notify', 'PaymentController@alipayNotify')->name('payment.alipay.notify');    // 支付宝服务器回调
Route::get('products/{product}', 'ProductsController@show')->name('products.show');




// // 用户身份验证相关的路由
// Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
// Route::post('login', 'Auth\LoginController@login');
// Route::post('logout', 'Auth\LoginController@logout')->name('logout');
// // 用户注册相关路由
// Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
// Route::post('register', 'Auth\RegisterController@register');
// // 密码重置相关路由
// Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
// Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
// Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
// Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');
// // Email 认证相关路由
// Route::get('email/verify', 'Auth\VerificationController@show')->name('verification.notice');
// Route::get('email/verify/{id}', 'Auth\VerificationController@verify')->name('verification.verify');
// Route::get('email/resend', 'Auth\VerificationController@resend')->name('verification.resend');