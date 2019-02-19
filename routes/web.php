<?php

Auth::routes(['verify' => true]);

Route::get('/', 'ProductsController@index')->name('products.index');

Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::resource('user_addresses', 'UserAddressesController');

});
