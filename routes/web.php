<?php

Auth::routes(['verify' => true]);

Route::get('/', 'ProductsController@index')->name('products.index');


Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::resource('user_addresses', 'UserAddressesController');   // 收获地址
    Route::post('products/{product}/favor', 'ProductsController@favor')->name('products.favor');    // 点击收藏
    Route::delete('products/{product}/disfavor', 'ProductsController@disfavor')->name('products.disfavor'); // 取消收藏
    Route::get('products/favorites', 'ProductsController@favorites')->name('products.favorites');   // 收藏列表
    Route::post('carts', 'CartItemsController@add')->name('carts.add'); // 添加购物车
    Route::get('carts', 'CartItemsController@index')->name('carts.index');  // 购物车列表
    Route::delete('carts/{productSku}', 'CartItemsController@remove')->name('carts.remove'); // 删除购物车
    Route::post('orders', 'OrdersController@store')->name('orders.store');
    Route::get('orders', 'OrdersController@index')->name('orders.index');
});

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